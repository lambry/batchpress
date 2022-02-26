# BatchPress

BatchPress is a little plugin to help process data in batches, with BatchPress you can run, monitor and cancel batched jobs.

![screenshot](screenshot.png)

## Usage

To use BatchPress create a new class per job and register those classes using the `batchpress/jobs` filter.

Job class outline:

- Required: `label` property to describe the job.
- Required: `process` method which is passed a single item for processing; any info/errors can be returned and will be displayed in the log.
- Optional|Required: `items` method is optional if the upload property is set to true, in this case it can be used to filter the uploaded content before staring the job. If upload if false or not defined the method is then required to return an array of items for processing.
- Optional: `upload` property to tell BatchPress if a CSV upload is required.
- Optional: `batch` property to set the number of items to process per batch.
- Optional: `title` property to override the default title displayed for the job.

## Basic Example

```php
class Update {
  public $batch = 10;
  public $label = 'Update data';

  // Prepare an array of items for processing
  public function items() : array { }

  // Process each item and optionally return log info
  public function process($item) : mixed { }
}
```

## Example with CSV upload

```php
class Import {
  public $batch = 10;
  public $upload = true;
  public $label = 'Import data';

  // Optionally filter and format the uploaded content before processing
  public function items(array $data) : array { }

  // Process each item and optionally return log info
  public function process($item) : mixed { }
}
```

## Registering jobs

```php
add_filter('batchpress/jobs', fn() => [Update::class, Import::class]);
```
