<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMSImage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TestimonialImageController extends Controller {
    /**
     * Display the current testimonial page image.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request): View | JsonResponse {
        try {
            if ($request->ajax()) {
                $data = CMSImage::where('page', 'testimonial')->latest()->get();
                return response()->json([
                    'status' => true,
                    'data'   => $data,
                ]);
            }
            $currentImage = CMSImage::where('page', 'testimonial')->latest()->first();
            return view('backend.layouts.cms.testimonial.index', compact('currentImage'));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the home page testimonial image. If an image already exists, it is replaced.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation error', 422, null, $validator->errors()->toArray());
        }

        try {
            $uploadedFile = $request->file('image');
            $filePath     = Helper::fileUpload($uploadedFile, 'testimonial', 'testimonial');

            if (!$filePath) {
                return Helper::jsonResponse(false, 'File upload failed. Please try again.', 500);
            }

            $existingImage = CMSImage::where('page', 'testimonial')->first();
            if ($existingImage) {
                // Delete the old file and update the record.
                Helper::fileDelete(public_path($existingImage->image));
                $existingImage->update([
                    'image' => $filePath,
                ]);
            } else {
                CMSImage::create([
                    'image' => $filePath,
                    'page'  => 'testimonial',
                ]);
            }

            return Helper::jsonResponse(true, 'Home page testimonial image updated successfully.', 200, ['newImageUrl' => asset($filePath)]);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred while updating the home page testimonial image: ' . $e->getMessage(), 500);
        }
    }
}
