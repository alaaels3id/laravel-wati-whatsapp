# Laravel WATI WhatsApp Integration Package

A clean, modern Laravel package for integrating with the **WATI WhatsApp Business API**.

## Installation

Install the package via [Composer](https://getcomposer.org):

```bash
composer require alaaelsaid/laravel-wati-whatsapp
```

## Publishing Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="wati-config"
```

## Environment Variables

Add the following keys to your `.env` file:

```dotenv
WATI_TEMPLATE=your_default_template_name
WATI_ENDPOINT=https://live-server-123.wati.io
WATI_ACCESS_TOKEN=your_wati_access_token
WATI_DEFAULT_COUNTRY_CODE=966
```

## Usage

### 1. Sending Single WhatsApp Template Message

```php
use Alaaelsaid\LaravelWatiWhatsapp\Facade\Whatsapp;

// Send using default template from config
Whatsapp::send('+966501234567', "Hello world", "John Smith");

// Send using custom template & custom parameters
Whatsapp::send(
    phone: '+966501234567',
    template: 'order_update_template',
    customParams: [
        ['name' => 'name', 'value' => 'John Smith'],
        ['name' => 'order_id', 'value' => 'ORD-9921'],
        ['name' => 'status', 'value' => 'Shipped']
    ]
);
```

### 2. Sending Bulk Template Messages (`multi`)

```php
// $users can be an array or Eloquent collection
Whatsapp::multi(
    message: "Your appointment is confirmed.",
    users: $users,
    column: 'phone',
    name: 'name'
);
```

### 3. Sending Direct Session Message (`sendSessionMessage`)

Send direct session text message outside of template broadcasts:

```php
Whatsapp::sendSessionMessage('+966501234567', 'Thank you for reaching out!');
```

### 4. Phone Number Utility (`MobilePhone`)

```php
use Alaaelsaid\LaravelWatiWhatsapp\Facade\MobilePhone;

// Convert Arabic numerals to English numerals
$number = MobilePhone::to_english_number('۰۱۲۳٤٥٦۷۸۹'); // Outputs: "0123456789"

// Format phone with country prefix
$prefixed = MobilePhone::setCountryCode('966')->prefixed('0501234567'); // Outputs: "+966501234567"
```