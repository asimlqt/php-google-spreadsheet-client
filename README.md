# Introduction

This library provides a simple interface to the Google Spreadsheet API.

There are a couple of important things to note.

* This library requires a valid OAuth access token to work but does not provide any means of generating one. The [Google APIs Client Library for PHP](https://code.google.com/p/google-api-php-client/) has all the functionality required for for generating and refreshing tokens so it would have been a waste of time duplicating the official google library.
* You can not create spreadsheets using this (PHP Google Spreadsheet Client) library, as creating spreadsheets is not part of the Spreadsheet API and the functionality already exists in the official Google Client Library.

I strongly encourage you to read through the [official Google Spreadsheet API documentation](https://developers.google.com/google-apps/spreadsheets) to get a grasp of the concepts.

# Usage

## Bootstrapping

The first thing you will need to do is include the autoloader and initializing the service request factory:

```php
require_once 'src/Google/Spreadsheet/Autoloader.php';

$accessToken = 'ya29.HES6ZQ2ar4xug3nQ-HozDTZ9Nw';
$request = new Google\Spreadsheet\Request($accessToken);
$serviceRequest = new Google\Spreadsheet\DefaultServiceRequest($request);
Google\Spreadsheet\ServiceRequestFactory::setInstance($serviceRequest);
```

## Retrieving a list of spreadsheets

```php
$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
```

SpreadsheetFeed implements ArrayIterator so you can iterate over it using a foreach loop or you can retrieve a single spreadsheet by name.

```php
$spreadsheet = $spreadsheetFeed->getByTitle('MySpreadsheet');
```

## Retrieving a list of worksheets

You can retrieve a list of worksheets from a spreadsheet by calling the getWorksheets() method.

```php
$worksheetFeed = $spreadsheet->getWorksheets();
```

You can loop over each worksheet or get a single worksheet by title.

```php
$worksheet = $worksheetFeed->getByTitle('Sheet 1');
```

## Retrieving a list based feed

```php
$listFeed = $worksheet->getListFeed();
$entries = $listFeed->getEntries();
```

## Inserting a new row into a worksheet

```php
$row = array('name'=>'John', 'age'=>25);
$listFeed->insert($row);
```