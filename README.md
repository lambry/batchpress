# BatchPress

BatchPress is a little starter plugin to help process data in batches, with BatchPress you can run, monitor and cancel batched jobs.

## Usage

To use BatchPress create a new class in the jobs folder for each job you need to run.

Job class outline:
- Required: `label` property to describe the job.
- Required: `items` method to return an array of items for processing. If the job requires a CSV an array of entries will be passed to items which can then be formatted and returned.
- Required: `process` method which is passed a single item for processing; any errors can be returned and added to the error log.
- Optional: `batch` property to set the number of items to process per batch.
- Optional: `upload` property which tells BatchPress the job requires a CSV upload.

#### Note
BatchPress doesn't process anything out of the box, it's meant purely as a starting point for creating quick plugins that need to run batched processes.

## Admin interface
![screenshot](screenshot.png)
