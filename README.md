<h1 align="center">Tiny Logger</h1>

[![Latest Stable Version](https://poser.pugx.org/serhii/tiny-logger/v/stable)](https://packagist.org/packages/serhii/tiny-logger)
[![Total Downloads](https://poser.pugx.org/serhii/tiny-logger/downloads)](https://packagist.org/packages/serhii/tiny-logger)
[![License](https://poser.pugx.org/serhii/tiny-logger/license)](https://packagist.org/packages/serhii/tiny-logger)
<a href="https://php.net/" rel="nofollow"><img src="https://camo.githubusercontent.com/2b1ed18c21257b0a1e6b8568010e6e8f3636e6d5/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f7068702d253345253344253230372e312d3838393242462e7376673f7374796c653d666c61742d737175617265" alt="Minimum PHP Version" data-canonical-src="https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg" style="max-width:100%;"></a>

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