<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Facade;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WatiService
{
    /**
     * @throws Exception
     */
    public function send($phone, $message, $name): object|null
    {
        try
        {
            $url = $this->url() . '/api/v1/sendTemplateMessage?whatsappNumber=' . $phone;

            $response = Http::withToken($this->token())->post($url, $this->singleNumberData($message, $name))->object();

            if (!$response->result)
            {
                Log::error($response->errors?->error ?? $response->info);
            }

            return $response;
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function multi($message, $users, $column = 'whatsapp', $name = 'name'): ?object
    {
        $url = $this->url() . '/api/v1/sendTemplateMessages';

        $response = Http::withToken($this->token())->post($url, $this->multiNumbersData($message, $users, $column, $name))->object();

        if (!$response->result)
        {
            Log::error($response->errors->error);
        }

        return $response;
    }

    private function singleNumberData($message, $name): array
    {
        return [
            'template_name'  => config('wati.template'),
            'broadcast_name' => 'string',
            'parameters'     => [
                ['name' => 'name', 'value' => $name],
                ['name' => 'message', 'value' => mb_convert_encoding($message, 'UTF-8', 'UTF-8')],
            ],
        ];
    }

    private function multiNumbersData($message, $users, $column, $name): array
    {
        return [
            'template_name'  => config('wati.template'),
            'broadcast_name' => 'string',
            'receivers'      => $this->setUsers($users, $message, $column, $name),
        ];
    }

    private function setUsers($users, $message, $column, $name): array
    {
        $numbers = [];

        foreach ($users as $user)
        {
            if (!$user->$column) continue;

            $code = str($user->$column)->startsWith('1') ? '020' : '966';

            $numbers[] = [
                'whatsappNumber' => MobilePhone::setCountryCode($code)->setPrefix($user->$column),
                'customParams'   => [
                    ['name' => 'name', 'value' => $user->$name],
                    ['name' => 'message', 'value' => $message],
                ],
            ];
        }

        return $numbers;
    }

    private function url()
    {
        return config('wati.end_point');
    }

    private function token()
    {
        return config('wati.access_token');
    }
}