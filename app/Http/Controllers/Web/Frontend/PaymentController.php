<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Payment;
use App\Models\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller {
    /**
     * Display the payment form page.
     *
     * @param Booking $booking
     * @return RedirectResponse|View|JsonResponse
     */
    public function makePayment(Booking $booking): RedirectResponse | View | JsonResponse {
        try {
            if ($booking->user_id !== Auth::id()) {
                return redirect()->route('beauty-expert-dashboard')->with('t-error', 'Unauthorized access.');
            }

            // Convert service_ids CSV to an array of integers.
            $serviceIds = $booking->service_ids ? array_map('intval', explode(',', $booking->service_ids)) : [];

            // Get the beauty expert's user ID from the "primary" user service.
            $serviceProviderId = $booking->userService->user_id;

            // Load all selected user_services related to those service IDs.
            $selectedServices = UserService::with('service')
                ->where('user_id', $serviceProviderId)
                ->whereIn('service_id', $serviceIds)
                ->get();

            // Calculate discount percentage based on the booking’s service_type.
            $discountPercentage = $booking->service_type === 'salon_services' ? 10 : 0;
            // Sum up the total price from all selected services.
            $servicesTotal = $selectedServices->sum('total_price');

            return view('frontend.layouts.payment.index', compact(
                'booking',
                'selectedServices',
                'discountPercentage',
                'servicesTotal'
            ));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function checkout(Booking $booking): JsonResponse {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'USD',
                        'product_data' => [
                            'name' => $booking->userService->service->services_name,
                        ],
                        'unit_amount'  => $booking->price * 100,
                    ],
                    'quantity'   => 1,
                ]],
                'mode'                 => 'payment',
                'success_url'          => route('payment.success', ['booking' => $booking->id]),
                'cancel_url'           => route('payment.cancel'),
            ]);

            return response()->json(['id' => $session->id]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function success(Booking $booking, Request $request): JsonResponse | View {
        try {
            Payment::create([
                'user_id'           => Auth::id(),
                'booking_id'        => $booking->id,
                'amount'            => $booking->price,
                'currency'          => 'USD',
                'payment_status'    => 'completed',
                'stripe_payment_id' => $request->get('session_id'),
            ]);

            Order::create([
                'user_id'      => Auth::id(),
                'booking_id'   => $booking->id,
                'total_amount' => $booking->price,
                'status'       => 'paid',
            ]);

            return view('frontend.layouts.payment.booking-successful')->with('t-success', 'Payment successful!');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cancel(): RedirectResponse | JsonResponse {
        try {
            return redirect()->route('beauty-expert-dashboard')->with('t-error', 'Payment cancelled.');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
