<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnquiryRequest;
use App\Models\Enquiry;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(Request $request): View
    {
        $product = null;
        if ($slug = $request->query('product')) {
            $product = Product::active()->where('slug', (string) $slug)->first();
        }

        return view('pages.contact', [
            'product' => $product,
            'locations' => Location::active()->ordered()->get(),
        ]);
    }

    public function store(EnquiryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('website');

        $enquiry = Enquiry::create($data + [
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
        ]);

        // Optional email alert to the company (Admin → Website Settings → Email / SMTP).
        // A mail failure must never lose the enquiry, which is already saved above.
        if (($notify = setting('enquiry_notify_email')) && \App\Support\MailSettings::isConfigured()) {
            try {
                \Illuminate\Support\Facades\Mail::to($notify)->send(new \App\Mail\NewEnquiryMail($enquiry->load('product')));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Enquiry #{$enquiry->id} alert email failed: ".$e->getMessage());
            }
        }

        $waText = "Hello, I have submitted an enquiry (Ref #{$enquiry->id}).\n"
            ."Name: {$enquiry->name}\n"
            .($enquiry->company_name ? "Company: {$enquiry->company_name}\n" : '')
            ."Subject: {$enquiry->subject}";

        return redirect()
            ->to(route('contact').'#enquiry')
            ->with('success', "Thank you, {$enquiry->name}. Your enquiry (Ref #{$enquiry->id}) has been received. Our team will contact you shortly.")
            ->with('whatsapp_followup', whatsapp_url($waText));
    }
}
