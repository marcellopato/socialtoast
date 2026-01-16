<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Prompt;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class DocumentUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $isAuditing = false;

    protected $rules = [
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
    ];

    public function updatedFile()
    {
        $this->validate();
    }

    public function save(GeminiService $geminiService)
    {
        $this->validate();

        $this->isAuditing = true;

        try {
            // Uniquely name the file or use original
            $filename = time() . '_' . $this->file->getClientOriginalName();
            $path = $this->file->storeAs('documents', $filename, 'public');

            // Create Document record
            $document = Document::create([
                'user_id' => Auth::id(),
                'path' => $path,
                'original_name' => $this->file->getClientOriginalName(),
                'mime_type' => $this->file->getMimeType(),
                'status' => 'processing',
            ]);

            // Retrieve Prompt (assumes 'auditor_persona' exists)
            $prompt = Prompt::where('key', 'auditor_persona')->first();
            $promptText = $prompt ? $prompt->content : "Please audit this document.";

            // Upload to Gemini
            // Note: In production, you might want to use the absolute path or read stream
            $fullPath = storage_path("app/public/{$path}");
            $fileUri = $geminiService->uploadFile($fullPath, $this->file->getMimeType());

            if ($fileUri) {
                // Audit
                $result = $geminiService->analyzeDocument($fileUri, $promptText, $this->file->getMimeType());

                // Update Document
                $keywordReason = $result['reason'] ?? 'No reason provided';
                $isApproved = $result['approved'] ?? false;

                $document->update([
                    'status' => $isApproved ? 'approved' : 'rejected',
                    'audit_result' => json_encode($result),
                    'audit_reason' => $keywordReason,
                ]);

                // Send Email Notification
                \Illuminate\Support\Facades\Mail::to('admin@socialtoast.com')->send(new \App\Mail\DocumentUploaded($document));
            } else {
                $document->update(['status' => 'failed', 'audit_reason' => 'Failed to upload to AI provider']);
            }

            session()->flash('message', 'Document uploaded and audited successfully.');
        } catch (\Exception $e) {
            Log::error('Upload Error: ' . $e->getMessage());
            session()->flash('error', 'Something went wrong: ' . $e->getMessage());
        } finally {
            $this->isAuditing = false;
            $this->reset('file');
        }
    }

    public function render()
    {
        return view('livewire.document-upload', [
            'recentDocuments' => Document::where('user_id', Auth::id())->latest()->take(5)->get()
        ]);
    }
}
