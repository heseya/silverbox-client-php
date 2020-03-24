# Silverbox PHP Client
PHP Client for [Silverbox API](https://github.com/heseya/silverbox).

## Requirements
PHP 7.1+ with curl extension.

## Installation
You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/).

```
composer require heseya/silverbox-client
```

## How to use?
Upload a file.

```php
$silverbox = new Silverbox('http://localhost');

$silverbox->as('client', 'key')->upload('photo.jpg');
```

Get public file url.

```php
$silverbox->as('client')->url($fileName);
```

Get private file.

```php
$silverbox->host('http://example.com')->get($fileName);
```

Get file info.

```php
$silverbox->info($fileName);
```

Delete a file.

```php
$silverbox->delete($fileName)
```

## License
Released under the MIT License. Please see [License File](https://github.com/heseya/silverbox-client-php/blob/master/LICENSE) for details.
