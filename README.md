# A Filament plugin to display Volet Feedback Messages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mydnic/volet-feedback-messages-filament-plugin.svg?style=flat-square)](https://packagist.org/packages/mydnic/volet-feedback-messages-filament-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/volet-feedback-messages-filament-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mydnic/volet-feedback-messages-filament-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/volet-feedback-messages-filament-plugin/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mydnic/volet-feedback-messages-filament-plugin/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mydnic/volet-feedback-messages-filament-plugin.svg?style=flat-square)](https://packagist.org/packages/mydnic/volet-feedback-messages-filament-plugin)

Add a simple Resource page to your Filament panel to display Volet Feedback Messages.

## Installation

You can install the package via composer:

```bash
composer require mydnic/volet-feedback-messages-filament-plugin
```

## Usage

```php
use Mydnic\VoletFeedbackMessagesFilamentPlugin\VoletFeedbackMessagesFilamentPlugin;
// ...

return $panel
    ->plugin(new VoletFeedbackMessagesFilamentPlugin());
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mydnic](https://github.com/Mydnic)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
