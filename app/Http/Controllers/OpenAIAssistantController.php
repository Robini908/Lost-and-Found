<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAIAssistantController extends Controller
{
    public function createAssistant()
    {
        // OpenAI API endpoint for creating an assistant
        $url = 'https://api.openai.com/v1/assistants';

        // OpenAI API key from .env
        $apiKey = env('OPENAI_API_KEY');

        // Request payload
        $payload = [
            'instructions' => 'You are a helpful assistant for a lost and found application.',
            'name' => 'Lost and Found Assistant',
            'model' => 'gpt-3.5-turbo', // Use a supported model
        ];

        // Send the request to create an assistant
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
            'OpenAI-Beta' => 'assistants=v2', // Required for Assistants API
        ])->post($url, $payload);

        // Check if the request was successful
        if ($response->successful()) {
            $assistant = $response->json();

            // Save the assistant ID to the .env file
            $this->updateEnvFile('OPENAI_ASSISTANT_ID', $assistant['id']);

            return response()->json([
                'message' => 'Assistant created successfully!',
                'assistant_id' => $assistant['id'],
            ]);
        } else {
            // Handle the error
            return response()->json([
                'message' => 'Failed to create assistant.',
                'error' => $response->json(),
            ], $response->status());
        }
    }

    protected function updateEnvFile($key, $value)
    {
        $envFilePath = base_path('.env');
        $envContent = file_get_contents($envFilePath);

        // Check if the key already exists
        if (str_contains($envContent, $key)) {
            // Update the existing key
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            // Append the new key
            $envContent .= "\n{$key}={$value}\n";
        }

        // Save the updated .env file
        file_put_contents($envFilePath, $envContent);
    }
}