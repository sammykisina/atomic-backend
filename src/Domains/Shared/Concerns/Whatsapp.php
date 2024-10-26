<?php

declare(strict_types=1);

namespace Domains\Shared\Concerns;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait Whatsapp
{
    public function sendWhatsAppNotification(array $data, string $template, ?string $imageUrl = null): void
    {
        // Attempt to send with image first
        $response = $this->sendMessage($data, $template, $imageUrl);

        // If sending with image fails, retry without image and add image URL to the data
        if ( ! $response->successful() && $imageUrl) {
            Log::info('Failed to send message with image. Retrying without image...');
            $data['image_url'] = $imageUrl; // Add image URL to data
            $this->sendMessage($data, $template);
        }
    }

    private function formatMessage(array $data, string $template): string
    {
        // Convert all values in $data to strings to avoid issues with non-string types
        $replaceData = array_map(fn($value) => (string) $value, $data);

        // Replace placeholders in the template with provided data
        foreach ($replaceData as $key => $value) {
            $template = str_replace('{{ ' . $key . ' }}', $value, $template);
        }

        // If image_url is not provided, remove the IMAGE line
        if (empty($data['image_url'])) {
            $template = preg_replace('/IMAGE: {{ image_url }}\s*/', '', $template);
        }

        return $template;
    }


    private function sendMessage(array $data, string $template, ?string $imageUrl = null): Response
    {
        $message = $this->formatMessage($data, $template);

        $payload = [
            'group' => config('whatsapp.wassenger.group_url'),
            'message' => $message,
        ];

        // Add media only if an image URL is provided
        if ($imageUrl) {
            $payload['media'] = [
                'url' => $imageUrl,
                'expiration' => '7d',
                'viewOnce' => false,
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('whatsapp.wassenger.auth_token'),
            'Accept' => 'application/json',
        ])->post(config('whatsapp.wassenger.base_url') . '/messages', $payload);

        if ($response->successful()) {
            Log::info('Message sent successfully');
        } else {
            Log::info('Failed to send message: ' . $response->body());
        }

        return $response;
    }
}
