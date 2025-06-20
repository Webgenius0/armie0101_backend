<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\AdminComment;
use App\Models\BookingCancellationAfterAppointment;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class AfterPaymentController extends Controller {
    /**
     * Display the list of all booking cancellation after payment.
     *
     * @param Request $request
     * @return View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request): View | JsonResponse {
        try {
            if ($request->ajax()) {
                $grouped = BookingCancellationAfterAppointment::with(['canceledBy', 'requestedBy'])
                    ->latest()
                    ->get()
                    ->groupBy('canceled_by')
                    ->values();

                return DataTables::of($grouped)
                    ->addIndexColumn()
                    ->addColumn('id', function ($group) {
                        return $group->first()->canceledBy->id ?? 'N/A';
                    })
                    ->addColumn('canceled_by_name', function ($group) {
                        $user = $group->first()->canceledBy;
                        return $user ? $user->first_name . ' ' . $user->last_name : 'N/A';
                    })
                    ->addColumn('email', function ($group) {
                        $user = $group->first()->canceledBy;
                        return $user ? ($user->email ?? 'N/A') : 'N/A';
                    })
                    ->addColumn('phone_number', function ($group) {
                        $user = $group->first()->canceledBy;
                        return $user ? ($user->phone_number ?? 'N/A') : 'N/A';
                    })
                    ->addColumn('requested_by_name', function ($group) {
                        // Gather all unique requested-by names from the group
                        $names = $group->map(function ($item) {
                            return $item->requestedBy ? $item->requestedBy->first_name . ' ' . $item->requestedBy->last_name : null;
                        })->filter()->unique();
                        return $names->isNotEmpty() ? $names->implode(', ') : 'N/A';
                    })
                    ->addColumn('ban_status', function ($group) {
                        $user = $group->first()->canceledBy;
                        if ($user && $user->banned_until && now()->lt($user->banned_until)) {
                            return '<span class="badge bg-danger">Banned until ' . $user->banned_until->format('Y-m-d H:i') . '</span>';
                        }
                        return '<span class="badge bg-success">Not Banned</span>';
                    })
                    ->addColumn('admin_comment', function ($group) {
                        $user    = $group->first()->canceledBy;
                        $comment = $user ? AdminComment::where('user_id', $user->id)->latest()->value('comment') : null;
                        if (!$comment) {
                            return 'N/A';
                        }
                        return strlen($comment) > 100 ? substr($comment, 0, 100) . '...' : $comment;
                    })
                    ->addColumn('full_comment', function ($group) {
                        $user    = $group->first()->canceledBy;
                        $comment = $user ? AdminComment::where('user_id', $user->id)->latest()->value('comment') : null;
                        return $comment ?: 'N/A';
                    })
                    ->addColumn('action', function ($group) {
                        $user         = $group->first()->canceledBy;
                        $canceledById = $user ? $user->id : 0;
                        return '<div class="d-flex justify-content-center hstack gap-3 fs-base">
                                <a href="javascript:void(0);" onclick="showBanModal(' . $canceledById . ')" class="link-danger text-decoration-none" title="Ban User">
                                    <i class="ri-forbid-line" style="font-size: 24px;"></i>
                                </a>

                                <a href="javascript:void(0);" onclick="showCommentModal(' . $canceledById . ')" class="link-primary text-decoration-none" title="Comment">
                                    <i class="ri-chat-3-line" style="font-size: 24px;"></i>
                                </a>
                            </div>';
                    })
                    ->rawColumns(['canceled_by_name', 'requested_by_name', 'ban_status', 'action'])
                    ->make();
            }

            return view('backend.layouts.booking-cancellation.after-payment.index');
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Ban a user for a specific duration.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function banUser(Request $request): JsonResponse {
        try {
            $request->validate([
                'user_id'  => 'required|exists:users,id',
                'duration' => 'required|in:1,3,5,7,10,15,30',
            ]);

            $user               = User::withBanned()->findOrFail($request->user_id);
            $user->banned_until = now()->addDays((int) $request->duration);
            $user->saveQuietly();

            // Clear only the count-related caches, keep base data cached
            Cache::forget('all_styler_counts');
            Cache::forget('top_beauty_experts');

            return Helper::jsonResponse(
                true,
                "User has been banned for {$request->duration} day(s).",
                200
            );
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a comment for a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeComment(Request $request): JsonResponse {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'comment' => 'required|string',
            ]);

            AdminComment::updateOrCreate(
                ['user_id' => $request->user_id],
                ['comment' => $request->comment]
            );

            return Helper::jsonResponse(true, 'Comment saved successfully!', 200);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
