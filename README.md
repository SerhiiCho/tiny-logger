<h1 align="center">Tiny Logger</h1>

[![Build Status](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Factions-badge.atrox.dev%2FSerhiiCho%2Ftiny-logger%2Fbadge&style=flat)](https://actions-badge.atrox.dev/SerhiiCho/tiny-logger/goto)
[![Latest Stable Version](https://poser.pugx.org/serhii/tiny-logger/v/stable)](https://packagist.org/packages/serhii/tiny-logger)
[![Total Downloads](https://poser.pugx.org/serhii/tiny-logger/downloads)](https://packagist.org/packages/serhii/tiny-logger)
[![License](https://poser.pugx.org/serhii/tiny-logger/license)](https://packagist.org/packages/serhii/tiny-logger)
<a href="https://php.net/" rel="nofollow"><img src="https://camo.githubusercontent.com/2b1ed18c21257b0a1e6b8568010e6e8f3636e6d5/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f7068702d253345253344253230372e312d3838393242462e7376673f7374796c653d666c61742d737175617265" alt="Minimum PHP Version" style="max-width:100%;"></a>

Light weight composer package for file logging in PHP7.

## Set file path

For setting up the path for all log files you can call `setPath` method in your bootstrap file. Logger is a [singleton](https://en.wikipedia.org/wiki/Singleton_pattern) class and `new()` method returns an instance of it. 

> Please remember! If you want to use logger in a cron scripts or WordPress hook, you need to call `setPath()` at the very first step of the script execution, it means that your project might have multiple places where you need to set path for your logs. If you don't want to call `setPath()` you can just pass the path to a `tiny_log()` function as a third argument. _See example below in Usage section._

```php
use Serhii\TinyLogger\Logger;

Logger::new()->setPath('logs/errors.log'); // simple format
Logger::new()->setPath('logs/%s.log', 'errors'); // sprintf format
```

## Usage

This package comes with a function `tiny_log()` where second and third arguments are not required.

```php
tiny_log('Some error message');
// Output in file: [2020-01-12 04:09:16] error: Some error message

tiny_log('Some error message', 'info');
// Output in file: [2020-01-12 04:09:16] info: Some error message

tiny_log('Some error message', 'debug', 'logs/debug.log');
// It will create debug.log in logs directory.
````

You can also use Logger class if you want. It will do the same as using function.

```php
use \Serhii\TinyLogger\Logger;

Logger::new()->error('Some error message');
Logger::new()->info('Some info message');
Logger::new()->setPath('logs/debug.log')->debug('Some error message');
````

## Options

For using one of the available options you can optionally pass certain flag to `tiny_log()` function as the second argument. If you also need to pass error type just separate them with the pipe `|` character. See the example:

```php
tiny_log('Some error message', 'option'); // just passing option
tiny_log('Some error message', 'option|error'); // with error type 'error'
tiny_log('Some error message', 'option|info'); // with error type 'info'
```

#### Available options

- `pos` - Show position of the logger. In which file and on what line number it is. It is useful when you're debugging, to not forget where you put your logger. See the example of output:

```text
[2020-01-12 04:09:16] info: Some log message goes here
>>> /var/www/html/app/Services/App.php on line: 77.
```

## Get started

To install all php dependencies you need to have [Composer PHP package manager](https://getcomposer.org) installed on your machine. Then you need to run the command below in your root directory of the project.

```bash
composer require serhii/tiny-logger
```