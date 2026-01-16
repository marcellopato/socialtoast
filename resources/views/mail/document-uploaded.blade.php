<x-mail::message>
	# Document Audit Notification

	A new document has been processed.

	**File:** {{ $document->original_name }}
	**Status:** {{ ucfirst($document->status) }}
	**Reason:** {{ $document->audit_reason ?? 'N/A' }}

	@if($document->status === 'approved')
	The document successfully passed the audit.
	@else
	<span style="color: red; font-weight: bold;">ATTENTION: The document failed the audit!</span>
	@endif

	<x-mail::button :url="config('app.url')">
		View Dashboard
	</x-mail::button>

	Thanks,<br>
	{{ config('app.name') }}
</x-mail::message>