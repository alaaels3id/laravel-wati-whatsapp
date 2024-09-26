## Installation

You can install the package via [Composer](https://getcomposer.org).

```bash
composer require alaaelsaid/laravel-wati-whatsapp
```

## Publishing

After install publish file config

```bash
php artisan vendor:publish --tag="wati"
```

## Env
In the .env file you can add those keys:

```dotenv
WATI_TEMPLATE=template_name
WATI_ENDPOINT=endpoint
WATI_ACCESS_TOKEN=access_token
```

## Usage

```php
use Alaaelsaid\LaravelWatiWhatsapp\Facade\Whatsapp;

// to send single phone number;
Whatsapp::send('+201007153686', "hello world", "John Smith");

// to send multi phone numbers;
// $users => collection of users to send a mutiple phone numbers;
// column => whatsapp column in users table;
// name => the column name of the user in the users table ex: [ name, fullname ];

Whatsapp::multi(message: "hello world", users: $users, column: 'whatsapp', name: 'John Smith'');