<h1 align="center">Tiny Logger</h1>

[![Latest Stable Version](https://poser.pugx.org/serhii/tiny-logger/v/stable)](https://packagist.org/packages/serhii/tiny-logger)
[![Total Downloads](https://poser.pugx.org/serhii/tiny-logger/downloads)](https://packagist.org/packages/serhii/tiny-logger)
[![License](https://poser.pugx.org/serhii/tiny-logger/license)](https://packagist.org/packages/serhii/tiny-logger)

Light weight composer package for file logging in PHP7.

## Set file path

For setting up the path for all log files you can call `setPath` method in your bootstrap file. Logger is a [singleton](https://en.wikipedia.org/wiki/Singleton_pattern) class and `new()` method returns an instance of it.

```php
\Serhii\TinyLogger\Logger::new()->setPath('logs/errors.log');
```

## Usage

This package comes with a function `tiny_log()` where second and third arguments are not required.

```php
tiny_log('Some error message');
tiny_log('Some error message', 'info');
tiny_log('Some error message', 'debug', 'logs/debug.log');
````

You can also use Logger class if you want.

```php
use \Serhii\TinyLogger\Logger;

Logger::new()->write('Some error message');
Logger::new()->write('Some error message', 'info');
Logger::new()->setPath('logs/debug.log')->write('Some error message', 'debug');
````

## Get started

```bash
composer require serhii/tiny-logger
```