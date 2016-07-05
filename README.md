[![Build Status](https://travis-ci.org/asimlqt/php-google-spreadsheet-client.svg?branch=master)](https://travis-ci.org/asimlqt/php-google-spreadsheet-client)

> Note: This library has been updated to v3 and it is not backwards compatible with v2 so please test/update your code appropriately before upgrading.

# Contents
* [Introduction](#introduction)
* [Installation](#installation)
* [Bootstrapping](#bootstrapping)
* [Spreadsheet](#spreadsheet)
    * [Retrieving a list of spreadsheets](#retrieving-a-list-of-spreadsheets)
    * [Retrieving a public spreadsheet](#retrieving-a-public-spreadsheet)
* [Worksheet](#worksheet)
    * [Retrieving a list of worksheets](#retrieving-a-list-of-worksheets)
    * [Adding a worksheet](#adding-a-worksheet)
    * [Adding headers to a new worksheet](#adding-headers-to-a-new-worksheet)
    * [Deleting a worksheet](#deleting-a-worksheet)
* [List feed](#list-feed)
    * [Retrieving a list feed](#retrieving-a-list-feed)
    * [Adding a list row](#adding-a-list-row)
    * [Updating a list row](#updating-a-list-row)
* [Cell feed](#cell-feed)
    * [Retrieving a cell feed](#retrieving-a-cell-feed)
    * [Updating a cell](#updating-a-cell)
* [Batch request](#updating-multiple-cells-with-a-batch-request)

# Introduction

This library provides a simple interface to the Google Spreadsheet API v3.

There are a couple of important things to note.

* This library requires a valid OAuth access token to work but does not provide any means of generating one. The [Google APIs Client Library for PHP](https://github.com/google/google-api-php-client) has all the functionality required for generating and refreshing tokens so it would have been a waste of time duplicating the official Google library.
* You can not create spreadsheets using this library, as creating spreadsheets is not part of the Google Spreadsheet API, rather it's part of the Google Drive API. See the official [Google APIs Client Library for PHP](https://github.com/google/google-api-php-client).

I strongly recommend you read through the [official Google Spreadsheet API documentation](https://developers.google.com/google-apps/spreadsheets) to get a grasp of the concepts.

# Usage

## Installation

Using [Composer](https://getcomposer.org/) is the recommended way to install it.

1 - Add "asimlqt/php-google-spreadsheet-client" as a dependency in your project's composer.json file.

```json
{
    "require": {
        "asimlqt/php-google-spreadsheet-client": "3.0.*"
    }
}
```

2 - Download and install Composer.

```
curl -sS https://getcomposer.org/installer | php
```

3 - Install your dependencies.

```
php composer.phar install
```

4 - Require Composer's autoloader.

```
require 'vendor/autoload.php';
```


## Bootstrapping

The first thing you will need to do is initialize the service request factory:

```php
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);
```

> Note: For Windows users, you can disable the ssl verification by '$serviceRequest->setSslVerifyPeer(false)'

## Spreadsheet

### Retrieving a list of spreadsheets

```php
$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();
```

Once you have a SpreadsheetFeed you can iterate over the spreadsheets using a foreach loop by calling the 'getEntries()' method or you can retrieve a single spreadsheet by it's title.

```php
$spreadsheet = $spreadsheetFeed->getByTitle('MySpreadsheet');
```

> Note: The 'getByTitle' method will return the first spreadsheet found with that title if you have more than one spreadsheet with the same name.

### Retrieving a public spreadsheet

A public spreadsheet is one that has been "published to the web". This does not require authentication. e.g.

```php
$serviceRequest = new DefaultServiceRequest("");
ServiceRequestFactory::setInstance($serviceRequest);

$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$worksheetFeed = $spreadsheetService->getPublicSpreadsheet("spreadsheet-id");
```

The spreadsheet id can be copied from the url of the actual spreadsheet in Google Drive.

## Worksheet

### Retrieving a list of worksheets

You can retrieve a list of worksheets from a spreadsheet by calling the getWorksheets() method.

```php
$spreadsheet = $spreadsheetFeed->getByTitle('MySpreadsheet');
$worksheetFeed = $spreadsheet->getWorksheetFeed();
```

You can loop over each worksheet using 'getEntries()' or retrieve a single worksheet by it's title.

```php
$worksheet = $worksheetFeed->getByTitle('Sheet1');
```

### Adding a worksheet

To create a new worksheet simply use the 'addWorksheet()' method. This takes 3 arguments:
- Worksheet name
- Number of rows
- Number of columns

```php
$spreadsheet->addWorksheet('New Worksheet', 50, 20);
```

### Adding headers to a new worksheet

The Google Spreadsheet API does not allow you to update a list row if headers are not already assigned. So, when you create a new worksheet, before you can add data to a worksheet using the 'Adding/Updating a list row' methods above, you need to add headers.

To add headers to a worksheet, use the following:
```php
$cellFeed = $worksheet->getCellFeed();

$cellFeed->editCell(1,1, "Row1Col1Header");
$cellFeed->editCell(1,2, "Row1Col2Header");
```

The only required parameter is the worksheet name, The row and column count are optional. The default value for rows is 100 and columns is 10.

### Deleting a worksheet

It's also possible to delete a worksheet.

```php
$worksheet->delete();
```

## List feed

List feeds work at the row level. Each entry will contain the data for a specific row.

> Note: You can not use formulas with the list feed. If you want to use formulas then you must use the cell feed (described below).

### Retrieving a list feed

```php
$listFeed = $worksheet->getListFeed();
```

Once you have a list feed you can loop over each entry.

```php
foreach ($listFeed->getEntries() as $entry) {
    $values = $entry->getValues();
}
```

The getValues() method returns an associative array where the keys are the column names and the values are the cell content.

> Note: The Google api converts the column headers to lower case so the column headings might not appear to be the same as what you see in Google Drive using your browser. 

> Note: If there is data for a particular row which does not have a column header then Google randomly generates a header and as far as I know it always begins with an underscore. Bear in mind that this is not generated by this library.

You can also sort and filter the data so you only retrieve what is required, this is expecially useful for large worksheets.

```php
$listFeed = $worksheet->getListFeed(["sq" => "age > 45", "reverse" => "true"]);
```
To find out all the available options visit [https://developers.google.com/google-apps/spreadsheets/#sorting_rows](https://developers.google.com/google-apps/spreadsheets/#sorting_rows).

### Adding a list row

```php
$listFeed->insert(["name" => "Someone", "age" => 25]);
```

> When adding or updating a row the column headers need to match exactly what was returned by the Google API, not what you see in Google Drive.

### Updating a list row

```php
$entries = $listFeed->getEntries();
$listEntry = $entries[0];

$values = $listEntry->getValues();
$values["name"] = "Joe";
$listEntry->update($values);
```

## Cell feed

Cell feed deals with individual cells. A cell feed is a collection of cells (of type CellEntry)

### Retrieving a cell feed

```php
$cellFeed = $worksheet->getCellFeed();
```

### Updating a cell

You can retrieve a single cell from the cell feed if you know the row and column numbers for that specific cell.

```php
$cell = $cellFeed->getCell(10, 2);
```

You can then update the cell value using the 'update' method. The value can be a primitive value or a formula e.g.

```php
$cell->update("=SUM(B2:B9)");
```

### Updating multiple cells with a batch request

When attempting to insert data into multiple cells then consider using the batch request functionality to improve performance.

To use the batch request functionality you need access to a cell feed first. You can not use batch requests with list feeds.

```php
$cellFeed = $worksheet->getCellFeed();

$batchRequest = new Google\Spreadsheet\Batch\BatchRequest();
$batchRequest->addEntry($cellFeed->createCell(2, 1, "111"));
$batchRequest->addEntry($cellFeed->createCell(3, 1, "222"));
$batchRequest->addEntry($cellFeed->createCell(4, 1, "333"));
$batchRequest->addEntry($cellFeed->createCell(5, 1, "=SUM(A2:A4)"));

$batchResponse = $cellFeed->insertBatch($batchRequest);
```
