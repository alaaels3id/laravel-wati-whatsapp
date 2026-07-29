<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Tests\Feature;

use Alaaelsaid\LaravelWatiWhatsapp\Facade\MobilePhone;
use Alaaelsaid\LaravelWatiWhatsapp\Facade\WatiService;
use Alaaelsaid\LaravelWatiWhatsapp\Facade\Whatsapp;
use Alaaelsaid\LaravelWatiWhatsapp\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class WatiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('wati.template', 'welcome_template');
        config()->set('wati.end_point', 'https://live-wati-api.wati.io');
        config()->set('wati.access_token', 'test_token_123');
        config()->set('wati.default_country_code', '966');
    }

    public function test_service_provider_registers_wati_singleton(): void
    {
        $instance = app('Wati');

        $this->assertInstanceOf(WatiService::class, $instance);
    }

    public function test_mobile_phone_arabic_to_english_numeral_conversion(): void
    {
        $arabic   = '۰۱۲۳٤٥٦۷۸۹';
        $converted = MobilePhone::to_english_number($arabic);

        $this->assertEquals('0123456789', $converted);
    }

    public function test_mobile_phone_formatting(): void
    {
        $phone  = '0501234567';
        $prefixed = MobilePhone::setCountryCode('966')->prefixed($phone);

        $this->assertEquals('+966501234567', $prefixed);
    }

    public function test_send_template_message_payload(): void
    {
        Http::fake([
            'https://live-wati-api.wati.io/api/v1/sendTemplateMessage*' => Http::response([
                'result' => true,
                'info'   => 'Message sent successfully',
            ], 200),
        ]);

        $res = Whatsapp::send('966501234567', 'Hello Customer', 'John Doe');

        $this->assertTrue($res->result);
        $this->assertEquals('Message sent successfully', $res->info);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'https://live-wati-api.wati.io/api/v1/sendTemplateMessage?whatsappNumber=966501234567' &&
                $data['template_name'] === 'welcome_template' &&
                $data['parameters'][0]['value'] === 'John Doe';
        });
    }

    public function test_send_session_message(): void
    {
        Http::fake([
            'https://live-wati-api.wati.io/api/v1/sendSessionMessage/*' => Http::response([
                'result' => true,
                'info'   => 'Session message sent',
            ], 200),
        ]);

        $res = Whatsapp::sendSessionMessage('966501234567', 'Direct Session Text');

        $this->assertTrue($res->result);
    }
}
