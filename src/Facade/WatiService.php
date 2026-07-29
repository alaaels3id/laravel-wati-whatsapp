<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Facade;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WatiService
{
    /**
     * Send a single WhatsApp template message.
     */
    public function send(string $phone, string $message = '', string $name = '', ?string $template = null, array $customParams = []): ?object
    {
        try {
            $url = $this->url() . '/api/v1/sendTemplateMessage?whatsappNumber=' . urlencode($phone);

            $payload = $this->singleNumberData($message, $name, $template, $customParams);

            $response = Http::withToken($this->token())->post($url, $payload)->object();

            if (isset($response->result) && !$response->result) {
                $errorMsg = $response->errors?->error ?? $response->info ?? 'WATI API Error';
                Log::error('[WATI WhatsApp] Single Send Error: ' . (is_string($errorMsg) ? $errorMsg : json_encode($errorMsg)));
            }

            return $response;
        } catch (Throwable $e) {
            Log::error('[WATI WhatsApp] Exception in send(): ' . $e->getMessage());

            return (object) ['result' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send bulk WhatsApp template messages to multiple users.
     */
    public function multi(string $message, $users, string $column = 'whatsapp', string $name = 'name', ?string $template = null): ?object
    {
        try {
            $url = $this->url() . '/api/v1/sendTemplateMessages';

            $payload = $this->multiNumbersData($message, $users, $column, $name, $template);

            $response = Http::withToken($this->token())->post($url, $payload)->object();

            if (isset($response->result) && !$response->result) {
                $errorMsg = $response->errors?->error ?? 'WATI Bulk Send Error';
                Log::error('[WATI WhatsApp] Multi Send Error: ' . (is_string($errorMsg) ? $errorMsg : json_encode($errorMsg)));
            }

            return $response;
        } catch (Throwable $e) {
            Log::error('[WATI WhatsApp] Exception in multi(): ' . $e->getMessage());

            return (object) ['result' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send a direct session text message (outside of approved templates).
     */
    public function sendSessionMessage(string $phone, string $message): ?object
    {
        try {
            $url = $this->url() . '/api/v1/sendSessionMessage/' . urlencode($phone) . '?messageText=' . urlencode($message);

            $response = Http::withToken($this->token())->post($url)->object();

            if (isset($response->result) && !$response->result) {
                Log::error('[WATI WhatsApp] Session Message Error: ' . json_encode($response));
            }

            return $response;
        } catch (Throwable $e) {
            Log::error('[WATI WhatsApp] Exception in sendSessionMessage(): ' . $e->getMessage());

            return (object) ['result' => false, 'message' => $e->getMessage()];
        }
    }

    private function singleNumberData(string $message, string $name, ?string $template, array $customParams = []): array
    {
        $templateName = $template ?: config('wati.template');

        $parameters = !empty($customParams) ? $customParams : [
            ['name' => 'name', 'value' => $name],
            ['name' => 'message', 'value' => mb_convert_encoding($message, 'UTF-8', 'UTF-8')],
        ];

        return [
            'template_name'  => $templateName,
            'broadcast_name' => 'string',
            'parameters'     => $parameters,
        ];
    }

    private function multiNumbersData(string $message, $users, string $column, string $name, ?string $template): array
    {
        $templateName = $template ?: config('wati.template');

        return [
            'template_name'  => $templateName,
            'broadcast_name' => 'string',
            'receivers'      => $this->setUsers($users, $message, $column, $name),
        ];
    }

    private function setUsers($users, string $message, string $column, string $name): array
    {
        $numbers = [];

        foreach ($users as $user) {
            $phone = is_array($user) ? ($user[$column] ?? null) : ($user->$column ?? null);
            $userName = is_array($user) ? ($user[$name] ?? '') : ($user->$name ?? '');

            if (!$phone) {
                continue;
            }

            $formattedPhone = MobilePhone::setCountryCode()->setPrefix($phone);

            $numbers[] = [
                'whatsappNumber' => $formattedPhone,
                'customParams'   => [
                    ['name' => 'name', 'value' => (string) $userName],
                    ['name' => 'message', 'value' => (string) $message],
                ],
            ];
        }

        return $numbers;
    }

    private function url(): string
    {
        return (string) config('wati.end_point');
    }

    private function token(): string
    {
        return (string) config('wati.access_token');
    }
}