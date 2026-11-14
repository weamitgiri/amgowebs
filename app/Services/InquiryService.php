<?php

namespace App\Services;

use App\Mail\AdminInquiryNotification;
use App\Mail\CustomerInquiryConfirmation;
use App\Models\ActivityLog;
use App\Models\PackageInquiry;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InquiryService
{
    /**
     * Create an inquiry, record its history and dispatch notifications.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, Request $request): PackageInquiry
    {
        return DB::transaction(function () use ($data, $request) {
            // Store an optional requirements file on the private disk (never web-accessible).
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $file = $request->file('attachment');
                $data['attachment_name'] = Str::limit($file->getClientOriginalName(), 180, '');
                $data['attachment_path'] = $file->store('inquiries/'.now()->format('Y/m'), 'local');
            }

            $inquiry = PackageInquiry::create([
                ...collect($data)->except(['consent', 'nickname', 'attachment'])->all(),
                'reference' => $this->generateReference(),
                'status' => 'new',
                'source_page' => $request->headers->get('referer') ?: url()->previous(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            ]);

            $inquiry->statusHistory()->create([
                'from_status' => null,
                'to_status' => 'new',
                'note' => 'Inquiry submitted',
            ]);

            ActivityLog::record('inquiry.created', "New inquiry {$inquiry->reference} from {$inquiry->name}", $inquiry);

            $this->notify($inquiry);

            return $inquiry;
        });
    }

    private function notify(PackageInquiry $inquiry): void
    {
        // Confirmation to the customer.
        Mail::to($inquiry->email)->queue(new CustomerInquiryConfirmation($inquiry));

        // Notification to the admin / sales team.
        $adminEmail = Setting::get('inquiry_notify_email') ?: Setting::get('contact_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new AdminInquiryNotification($inquiry));
        }
    }

    private function generateReference(): string
    {
        do {
            $reference = 'INQ-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (PackageInquiry::where('reference', $reference)->exists());

        return $reference;
    }
}
