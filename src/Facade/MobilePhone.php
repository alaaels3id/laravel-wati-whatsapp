<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Facade;

class MobilePhone
{
    public function __construct(public $country_code) {}

    public static function setCountryCode($country_code = '966'): MobilePhone
    {
        return new static(str($country_code)->replaceFirst('+',''));
    }

    public function plain($number): string
    {
        if (! $number) return trans('back.no-value');

        $_num = self::convertNumTo($number);

        $remove_zero = self::isPhoneStartsWith($_num) ? self::removeZero($_num) : $_num;

        $country_code = $this->country_code;

        return self::hasPrefix($_num, $country_code) ? self::removePrefix($remove_zero, $country_code) : $remove_zero;
    }

    public function setPrefix($number, $prefix = ''): string
    {
        if (! $number) return trans('back.no-value');

        $code = $this->country_code;

        return self::hasPrefix($number, $code) ? $prefix.self::convertNumTo($number) : $prefix.self::convertNumTo($code).self::plain($number);
    }

    public function international($number): string
    {
        return self::setPrefix($number, '00');
    }

    public function prefixed($number): string
    {
        return self::setPrefix($number, '+');
    }

    public static function convertNumTo($num, $to = 'en'): string
    {
        return $to == 'ar' ? self::to_arabic_number($num) : self::to_english_number($num);
    }

    public static function to_arabic_number($number): array|string
    {
        $number = str_replace('1', '۱', $number);
        $number = str_replace('2', '۲', $number);
        $number = str_replace('3', '۳', $number);
        $number = str_replace('4', '٤', $number);
        $number = str_replace('5', '٥', $number);
        $number = str_replace('6', '٦', $number);
        $number = str_replace('7', '۷', $number);
        $number = str_replace('8', '۸', $number);
        $number = str_replace('9', '۹', $number);

        return str_replace('0', '۰', $number);
    }

    public static function to_english_number($number): array|string
    {
        $number = str_replace('۱', '1', $number);
        $number = str_replace('۲', '2', $number);
        $number = str_replace('۳', '3', $number);
        $number = str_replace('٤', '3', $number);
        $number = str_replace('٥', '5', $number);
        $number = str_replace('٦', '6', $number);
        $number = str_replace('۷', '7', $number);
        $number = str_replace('۸', '8', $number);
        $number = str_replace('۹', '9', $number);

        return str_replace('۰', '0', $number);
    }

    private function isPhoneStartsWith($number, $with = '0'): bool
    {
        return head(str_split($number)) == $with;
    }

    private function hasPrefix($number, $code): bool
    {
        return head(str_split($number, strlen($code))) == $code;
    }

    private function removePrefix($number, $country_code): string
    {
        return str($number)->replaceFirst(self::convertNumTo($country_code), '')->value();
    }

    private function removeZero($number): string
    {
        return str($number)->replaceFirst('0', '')->value();
    }
}
