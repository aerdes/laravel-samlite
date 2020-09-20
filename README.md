# Enable authentication against SAML identity providers for your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aerdes/laravel-samlite.svg?style=flat-square)](https://packagist.org/packages/aerdes/laravel-samlite)
[![Build Status](https://img.shields.io/travis/aerdes/laravel-samlite/master.svg?style=flat-square)](https://travis-ci.com/github/aerdes/laravel-samlite)
[![StyleCI](https://github.styleci.io/repos/261439333/shield?branch=master)](https://github.styleci.io/repos/261439333)
[![Total Downloads](https://img.shields.io/packagist/dt/aerdes/laravel-samlite.svg?style=flat-square)](https://packagist.org/packages/aerdes/laravel-samlite)

This package can be used to quickly add authentication against SAML2 identity providers to your Laravel application. This package thus makes your Laravel application a SAML2 service provider.

Please note that this package is based on [onelogin/php-saml](https://packagist.org/packages/onelogin/php-saml). It is similar to [aacotroneo/laravel-saml2](https://packagist.org/packages/aacotroneo/laravel-saml2) but as easy to use as [laravel/socialite](https://packagist.org/packages/laravel/socialite). It also tries to resemble the default Laravel authentication under the hood.


## Installation

You can install the package via composer:

```bash
composer require aerdes/laravel-samlite
```

## Usage

After installing the package make sure to set some environmental variables. For example, when you want to use [Microsoft Azure](https://portal.azure.com) as identity provider, please set up the following environmental variables:

```dotenv
SAML_IDP_AZURE_AD_IDENTIFIER=
SAML_IDP_AZURE_LOGIN_URL=
SAML_IDP_AZURE_LOGOUT_URL=
SAML_IDP_AZURE_CERT=
```

If your environmental file does not yet contain the variables `SAML_SP_PRIVATE_KEY` and `SAML_SP_CERT` also run:
```bash
php artisan saml:setup
```

You then want to create a Controller that extends the [authentication controller](src/Http/Controllers/SamlController.php) that ships with this package. Here is an example.

```php
<?php

namespace App\Http\Controllers;

use Aerdes\LaravelSamlite\Http\Controllers\SamlController;
use Aerdes\LaravelSamlite\SamlAuth;

class AuthenticationController extends SamlController
{
    
    public function loginUser(SamlAuth $saml_auth)
    {
        $mail = $saml_auth->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress')[0];
        $name = $saml_auth->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/displayname')[0];
        $user = User::where('email', $mail)->first();
        if (!$user) {
            $user = new User;
            $user->name = $name;
            $user->email = $mail;
            $user->password = md5(rand(1,10000));
            $user->save();
        }
        $this->guard()->loginUsingId($user->id);
    }

}
```

Finally, register your controller by placing another environmental variable:
```dotenv
SAML_CONTROLLER="App\Http\Controllers\AuthenticationController"
```

## Customization

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Aerdes\LaravelSamlite\SamlServiceProvider" --tag="config"
```

Feel free to set the appropriate environmental variables (or edit the config file) in order to add your preferred identity providers.

## Testing

You can run all tests via composer as well:

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email [lukas@aerdes.com](mailto:lukas@aerdes.com) instead of using the issue tracker.

## Postcardware

You are free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown. The address is: Lukas Müller, Dirklangendwarsstraat 5, 2611HZ Delft, The Netherlands.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.
