<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Models\PackageInquiry;
use App\Services\InquiryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request, InquiryService $service): RedirectResponse
    {
        $inquiry = $service->create($request->validated(), $request);

        return redirect()
            ->route('inquiry.thanks', $inquiry->reference)
            ->with('status', 'Your inquiry has been submitted successfully.');
    }

    public function thanks(string $reference): View
    {
        $inquiry = PackageInquiry::with('package')->where('reference', $reference)->firstOrFail();

        return view('site.inquiry-thanks', [
            'inquiry' => $inquiry,
            'seo' => [
                'title' => 'Inquiry Received',
                'description' => 'Your website inquiry has been received.',
                'robots' => 'noindex, nofollow',
            ],
        ]);
    }
}
