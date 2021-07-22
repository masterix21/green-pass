# COVID-19 Green Pass PHP Decoder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masterix21/green-pass.svg)](https://packagist.org/packages/masterix21/green-pass)
[![Total Downloads](https://img.shields.io/packagist/dt/masterix21/green-pass.svg)](https://packagist.org/packages/masterix21/green-pass)
[![Build Status](https://travis-ci.com/masterix21/green-pass.svg?branch=master)](https://travis-ci.com/masterix21/green-pass)
![psalm](https://github.com/masterix21/green-pass/actions/workflows/psalm.yml/badge.svg?style=flat-square)
[![codecov](https://codecov.io/gh/masterix21/green-pass/branch/master/graph/badge.svg?token=D7CW847KDA)](https://codecov.io/gh/masterix21/green-pass)

This is a simplier way to decode the Green Pass QR-code in your PHP application. 

## Support us

If you like the package, feel free to support us: it will help to improve our products.

## Installation

You can install the package via composer:

```bash
composer require masterix21/green-pass
```

## Usage

```php
\Masterix21\GreenPass\Services\Decoder::qrcode("HC1:.....");
```

![GreenPass object](https://github.com/masterix21/green-pass/blob/master/resources/green-pass-data.png?raw=true)

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email l.longo@ambita.it instead of using the issue tracker.

## Credits

-   [Luca Longo](https://github.com/masterix21)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

