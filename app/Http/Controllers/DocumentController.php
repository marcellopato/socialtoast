<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class DocumentController extends Controller
{
    public function preview(Document $document)
    {
        // Check authorization: User owns document or is Admin (can view all)
        if ($document->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        if (!Storage::disk('google')->exists($document->path)) {
            abort(404);
        }

        $fileContent = Storage::disk('google')->get($document->path);
        $mimeType = Storage::disk('google')->mimeType($document->path);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_name . '"');
    }
}
