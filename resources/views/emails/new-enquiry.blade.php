<x-mail::message>
# New website enquiry

A customer submitted the enquiry form on the website.

<x-mail::table>
| | |
|:--|:--|
| **Reference** | #{{ $enquiry->id }} |
| **Name** | {{ $enquiry->name }} |
| **Company** | {{ $enquiry->company_name ?: '—' }} |
| **Mobile** | {{ $enquiry->mobile }} |
| **Email** | {{ $enquiry->email ?: '—' }} |
| **Product** | {{ $enquiry->product?->name ?: '—' }} |
| **Subject** | {{ $enquiry->subject }} |
| **Received** | {{ $enquiry->created_at->format('d M Y, h:i A') }} |
</x-mail::table>

**Message**

<x-mail::panel>
{{ $enquiry->message }}
</x-mail::panel>

<x-mail::button :url="route('admin.enquiries.show', $enquiry)">
Open in Admin Panel
</x-mail::button>

{{ setting('site_name', config('app.name')) }}
</x-mail::message>
