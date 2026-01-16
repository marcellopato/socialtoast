<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
	protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
	protected string $apiKey;
	// Updated to the latest stable flash alias which should work with billing/free tier
	protected string $model = 'gemini-flash-latest';

	public function __construct()
	{
		$this->apiKey = config('services.gemini.api_key');
	}

	/**
	 * Upload file to Gemini File API
	 */
	public function uploadFile(string $filePath, string $mimeType): ?string
	{
		$fileSize = filesize($filePath);
		$url = "https://generativelanguage.googleapis.com/upload/v1beta/files?key={$this->apiKey}";

		// Simple upload (suitable for small files < 2GB)
		try {
			$response = Http::withHeaders([
				'X-Goog-Upload-Protocol' => 'raw',
				'X-Goog-Upload-Header-Content-Length' => $fileSize,
				'X-Goog-Upload-Header-Content-Type' => $mimeType,
				'Content-Type' => $mimeType,
			])
				->withBody(file_get_contents($filePath), $mimeType)
				->post($url);

			if ($response->successful()) {
				$data = $response->json();
				return $data['file']['uri'] ?? null;
			}

			Log::error('Gemini Upload Failed', ['status' => $response->status(), 'body' => $response->body()]);
			return null;
		} catch (\Exception $e) {
			Log::error('Gemini Upload Exception', ['message' => $e->getMessage()]);
			return null;
		}
	}

	/**
	 * Analyze document using uploaded file URI and persona prompt
	 */
	public function analyzeDocument(string $fileUri, string $promptText, string $mimeType = 'application/pdf'): array
	{
		$url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

		$payload = [
			'contents' => [
				[
					'role' => 'user',
					'parts' => [
						['text' => $promptText],
						['file_data' => ['mime_type' => $mimeType, 'file_uri' => $fileUri]]
					]
				]
			],
			'generationConfig' => [
				'temperature' => 0.2,
				'response_mime_type' => 'application/json',
			]
		];

		try {
			$response = Http::post($url, $payload);

			if ($response->successful()) {
				$data = $response->json();
				$text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
				return json_decode($text, true) ?? ['error' => 'Invalid JSON response', 'raw' => $text];
			}

			Log::error('Gemini Analysis Failed', ['status' => $response->status(), 'body' => $response->body()]);
			return ['error' => 'API Error: ' . $response->status()];
		} catch (\Exception $e) {
			Log::error('Gemini Analysis Exception', ['message' => $e->getMessage()]);
			return ['error' => 'Exception: ' . $e->getMessage()];
		}
	}
}
