# PHP Google Spreadsheet Client

This library provides a simple interface to the Google Spreadsheet API.

There are a couple of important things to note.

* This library requires a valid OAuth access token to work but does not provide any means of generating one. The [Google APIs Client Library for PHP](https://code.google.com/p/google-api-php-client/) has all the functionality required for for generating and refreshing tokens so it would have been a waste of time duplicating the official google library.
* You can not create spreadsheets using this (PHP Google Spreadsheet Client) library, as creating spreadsheets is not part of the Spreadsheet API and the functionality already exists in the official Google Client Library.

# Usage

## Bootstrapping

The first thing you will need to do is include the autoloader and set the access token:

```php
require_once 'src/Google/Spreadsheet/Autoloader.php';

$accessToken = 'ya29.HES6ZQ2Q-HozDTZ9Nw';
$request = new Google\Spreadsheet\new Request($accessToken);
$serviceRequest = new Google\Spreadsheet\DefaultServiceRequest($request);
ServiceRequestFactory::setInstance($serviceRequest);
```
