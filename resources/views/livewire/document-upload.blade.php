<div class="p-6 bg-white border-b border-gray-200 rounded-lg shadow-sm">
    <div class="max-w-xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Document Audit</h2>

        <!-- Upload Area -->
        <div
            x-data="{ isDropping: false, isUploading: false, progress: 0 }"
            x-on:livewire-upload-start="isUploading = true"
            x-on:livewire-upload-finish="isUploading = false"
            x-on:livewire-upload-error="isUploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
            class="relative border-4 border-dashed border-gray-300 rounded-xl p-8 flex flex-col items-center justify-center transition-colors duration-300 ease-in-out"
            :class="{ 'border-indigo-500 bg-indigo-50': isDropping }"
            @dragover.prevent="isDropping = true"
            @dragleave.prevent="isDropping = false"
            @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))">
            <input
                x-ref="fileInput"
                type="file"
                wire:model.live="file"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50"
                @dragenter="isDropping = true"
                @dragleave="isDropping = false"
                @drop="isDropping = false">

            <div class="text-center pointer-events-none" :class="{ 'opacity-50': isUploading }">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-1 text-sm text-gray-600">
                    <span class="font-medium text-indigo-600 hover:text-indigo-500">Upload a file</span> or drag and drop
                </p>
                <p class="text-xs text-gray-500">PDF, PNG, JPG up to 10MB</p>

                <p x-show="isUploading" class="text-sm font-bold text-indigo-600 mt-2">Uploading...</p>
            </div>

            <!-- Progress Bar -->
            <div x-show="isUploading" class="absolute bottom-4 left-4 right-4">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" :style="`width: ${progress}%`"></div>
                </div>
            </div>
        </div>

        @error('file') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror

        <!-- Review & Submit -->
        @if ($file && !$isAuditing)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-900 truncate">{{ $file->getClientOriginalName() }}</span>
            </div>
            <button wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <span wire:loading.remove wire:target="save">Audit Document</span>
                <span wire:loading wire:target="save">Auditing...</span>
            </button>
        </div>
        @endif

        @if($isAuditing)
        <div class="mt-4 text-center text-indigo-600 animate-pulse">
            Analyzing document with AI...
        </div>
        @endif

        @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <!-- Google Drive Info -->
    <div x-data="{ showGuide: false }" class="mt-8 border-t pt-6 mb-8">
        <button @click="showGuide = !showGuide" type="button" class="flex items-center text-sm text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>How to connect to Google Drive?</span>
        </button>

        <div x-show="showGuide" class="mt-4 p-4 bg-blue-50 text-sm text-blue-800 rounded-lg" style="display: none;">
            <p class="font-bold mb-2">Google Drive Integration Setup:</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Create a project in <strong>Google Cloud Console</strong>.</li>
                <li>Enable the <strong>Google Drive API</strong>.</li>
                <li>Create <strong>OAuth 2.0 Credentials</strong> (Client ID & Secret).</li>
                <li>Generate a <strong>Refresh Token</strong> (e.g., via OAuth Playground).</li>
                <li>Update your <code>.env</code> file with the credentials.</li>
            </ol>
            <div class="mt-2 text-xs text-gray-500">
                See <a href="https://github.com/marcellopato/SocialToast#google-drive-setup" target="_blank" class="underline">README</a> for details.
            </div>
        </div>
    </div>

    <!-- Recent Documents List -->
    <div class="mt-10">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Audits</h3>
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Document</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Reason</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($recentDocuments as $doc)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $doc->original_name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                                    {{ $doc->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($doc->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $doc->audit_reason }}">{{ $doc->audit_reason }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doc->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No documents audited yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>