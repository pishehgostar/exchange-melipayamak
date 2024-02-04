<h1>Melipayamak - Laravel Notification Channel</h1>

This package makes it easy to send notifications using [Melipayamak](https://www.melipayamak.com/) , with Laravel 9.x
## Installation
This package can be installed through Composer.

``` bash
composer require pishehgostar/exchange-melipayamak
```

## Setting up Melipayamak service
Add your melipayamak service information in services.php config file.

````php
return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    
    // ...
    
    'melipayamak'=>[
        'username'=>'###',
        'password'=>'###',
        'from'=>'###',
    ]
    
];
````
## Usage

In every model you wish to be notifiable via Melipayamak, you must add a channel ID property to that model accessible through a routeNotificationForMelipayamak method:

````php
class User extends Eloquent
{
    use Notifiable;

    public function routeNotificationForMelipayamak(Notification $notification): array|string
    {
        return $this->mobile;
    }
}
````

You may now tell Laravel to send notifications to melipayamak channels in the via method:

````php
class InvoicePaidNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['melipayamak'];
    }
    
    // send sms using patterns
    public function toMelipayamak(object $notifiable): MelipayamakSmsMessage
    {
        $pattern_code = '######';
        $pattern_parameters = ['first','second'];
        return (new MelipayamakSmsMessage)
            ->pattern($pattern_code,$pattern_parameters);
    }
    
    // send simple sms
    public function toMelipayamak(object $notifiable): MelipayamakSmsMessage
    {
        return (new MelipayamakSmsMessage)
            ->simple('salam');
    }
}
````