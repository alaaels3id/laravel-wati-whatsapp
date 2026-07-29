<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static object|null send(string $phone, string $message = '', string $name = '', ?string $template = null, array $customParams = [])
 * @method static object|null multi(string $message, $users, string $column = 'whatsapp', string $name = 'name', ?string $template = null)
 * @method static object|null sendSessionMessage(string $phone, string $message)
 *
 * @see WatiService
 */
class Whatsapp extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Wati';
    }
}