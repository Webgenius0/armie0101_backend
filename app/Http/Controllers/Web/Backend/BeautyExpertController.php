<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\StatusUpdateMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class BeautyExpertController extends Controller {
    /**
     * Display the list of all beauty expert users.
     *
     * @param Request $request
     * @return View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request): View | JsonResponse {
        try {
            if ($request->ajax()) {
                $data = User::where('role', 'beauty_expert')->latest()->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function ($data) {
                        return $data->first_name . ' ' . $data->last_name;
                    })
                    ->addColumn('status', function ($user) {
                        $sel = '<select class="form-select" onchange="changeStatus(' . $user->id . ', this.value, this)">';
                        $sel .= '<option value="active" ' . ($user->status == 'active' ? 'selected' : '') . '>Approved</option>';
                        $sel .= '<option value="inactive" ' . ($user->status == 'inactive' ? 'selected' : '') . '>Pending</option>';
                        $sel .= '</select>';
                        return $sel;
                    })
                    ->addColumn('action', function ($user) {
                        return '<div class="d-flex justify-content-center hstack gap-3 fs-base">
                                    <a href="javascript:void(0);" onclick="showUserDetails(' . $user->id . ')" class="link-primary text-decoration-none" title="View" data-bs-toggle="modal" data-bs-target="#viewUserModal">
                                        <i class="ri-eye-line" style="font-size: 24px;"></i>
                                    </a>

                                    <a href="javascript:void(0);" onclick="showDeleteConfirm(' . $user->id . ')" class="link-danger text-decoration-none" title="Delete">
                                        <i class="ri-delete-bin-5-line" style="font-size: 24px;"></i>
                                    </a>
                                </div>';
                    })
                    ->rawColumns(['name', 'status', 'action'])
                    ->make();
            }
            return view('backend.layouts.users.beauty_expert.index');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified beauty expert user details.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse {
        try {
            $user = User::with(['businessInformation', 'userServices.service', 'travelRadius'])->findOrFail($id);

            return response()->json([
                'name'          => $user->first_name . ' ' . $user->last_name,
                'email'         => $user->email,
                'phone_number'  => $user->phone_number,
                'role'          => $user->role,
                'status'        => $user->status,
                'business_info' => $user->businessInformation ? [
                    'business_name'    => $user->businessInformation->business_name,
                    'business_address' => $user->businessInformation->business_address,
                    'bio'              => $user->businessInformation->bio,
                    'avatar'           => asset($user->businessInformation->avatar),
                    'license'          => $user->businessInformation->license ? asset($user->businessInformation->license) : null,
                ] : null,
                'services'      => $user->userServices->map(function ($userService) {
                    return [
                        'service_name'  => $userService->service->services_name ?? 'N/A',
                        'offered_price' => $userService->offered_price,
                        'total_price'   => $userService->total_price,
                        'image'         => $userService->image ? asset($userService->image) : null,
                    ];
                }),
                'travel_radius' => $user->travelRadius ? [
                    'free_radius'       => $user->travelRadius->free_radius,
                    'travel_radius'     => $user->travelRadius->travel_radius,
                    'travel_charge'     => $user->travelRadius->travel_charge,
                    'max_radius'        => $user->travelRadius->max_radius,
                    'max_charge'        => $user->travelRadius->max_charge,
                    'min_booking_value' => $user->travelRadius->min_booking_value,
                ] : null,
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle the status of the specified beauty expert users.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function status(Request $request, int $id): JsonResponse {
        try {
            $user   = User::findOrFail($id);
            $status = $request->input('status');

            if ($status === 'active' || $status === 'inactive') {
                $user->status = $status;
                $user->save();

                // Queue the status update email
                // Mail::to($user->email)->queue(new StatusUpdateMail($user));

                // Send the status update email directly
                Mail::to($user->email)->send(new StatusUpdateMail($user));

                return response()->json([
                    'success' => true,
                    'message' => 'Status Updated Successfully',
                    'data'    => $user,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid status value.',
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified beauty expert users from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            if ($user->role === 'beauty_expert') {
                // Delete related business information
                $user->businessInformation()->delete();

                // Delete related user services
                $user->userServices()->delete();

                // Delete related travel radius
                $user->travelRadius()->delete();
            }

            // Delete related bookings and reviews
            $user->bookings()->delete();
            $user->reviews()->delete();
            $user->reports()->delete();
            $user->orders()->delete();
            $user->payments()->delete();

            // Delete the user
            $user->forceDelete();

            DB::commit();

            return response()->json([
                't-success' => true,
                'message'   => 'User and related information deleted successfully.',
            ]);
        } catch (Exception) {
            DB::rollBack();
            return response()->json([
                't-success' => false,
                'message'   => 'An error occurred. Please try again.',
            ]);
        }
    }
}
