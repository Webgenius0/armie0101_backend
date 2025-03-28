<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MailSettingsController extends Controller {
    /**
     * Display the Mail settings page if the user has the 'mail_setting' permission.
     *
     * @return View|RedirectResponse
     */
    public function index(): JsonResponse | View {
        try {
            // Load current mail settings from the environment
            $settings = [
                'mail_mailer'       => env('MAIL_MAILER', ''),
                'mail_host'         => env('MAIL_HOST', ''),
                'mail_port'         => env('MAIL_PORT', ''),
                'mail_username'     => env('MAIL_USERNAME', ''),
                'mail_password'     => env('MAIL_PASSWORD', ''),
                'mail_encryption'   => env('MAIL_ENCRYPTION', ''),
                'mail_from_address' => env('MAIL_FROM_ADDRESS', ''),
            ];

            return view('backend.layouts.settings.mail_settings', compact('settings'));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the Mail settings in the .env file.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $request->validate([
            'mail_mailer'       => ['nullable', 'string', 'regex:/^\S*$/'],
            'mail_host'         => ['nullable', 'string', 'regex:/^\S*$/'],
            'mail_port'         => ['nullable', 'string', 'regex:/^\S*$/'],
            'mail_username'     => ['nullable', 'string', 'regex:/^\S*$/'],
            'mail_password'     => ['nullable', 'string', 'regex:/^\S*$/'],
            'mail_encryption'   => ['nullable', 'string', 'regex:/^\S*$/'],
            'mail_from_address' => ['nullable', 'string'],
        ], [
            'regex' => 'The :attribute must not contain spaces.',
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/MAIL_MAILER=(.*)\s*/',
                '/MAIL_HOST=(.*)\s*/',
                '/MAIL_PORT=(.*)\s*/',
                '/MAIL_USERNAME=(.*)\s*/',
                '/MAIL_PASSWORD=(.*)\s*/',
                '/MAIL_ENCRYPTION=(.*)\s*/',
                '/MAIL_FROM_ADDRESS=(.*)\s*/',
            ], [
                'MAIL_MAILER=' . $request->mail_mailer . $lineBreak,
                'MAIL_HOST=' . $request->mail_host . $lineBreak,
                'MAIL_PORT=' . $request->mail_port . $lineBreak,
                'MAIL_USERNAME=' . $request->mail_username . $lineBreak,
                'MAIL_PASSWORD=' . $request->mail_password . $lineBreak,
                'MAIL_ENCRYPTION=' . $request->mail_encryption . $lineBreak,
                'MAIL_FROM_ADDRESS=' . '"' . $request->mail_from_address . '"' . $lineBreak,
            ], $envContent);

            File::put(base_path('.env'), $envContent);
            Artisan::call('optimize:clear');

            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
