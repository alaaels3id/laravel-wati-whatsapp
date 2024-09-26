<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static send(string $number, string $message, string $name)
 *
 * @see UrwayProcess
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