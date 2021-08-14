# BatchPress

BatchPress is a little starter plugin to help process data in batches, with BatchPress you can run, monitor and cancel batched jobs.

## Usage

To use BatchPress create a new class in the jobs folder for each job you need to run.

Job class outline:
- Required: `label` property to describe the job.
- Required: `process` method which is passed a single item for processing; any errors can be returned and added to the error log.
- Optional|Required: `items` method is optional if the upload property is set to true, in this case it can be used to filter the uploaded content before running the job. If upload if false or not defined the method is then required to return an array of items for processing.
- Optional: `upload` property to tell BatchPress if a CSV upload is required.
- Optional: `batch` property to set the number of items to process per batch.

#### Note
BatchPress doesn't process anything out of the box, it's meant purely as a starting point for creating quick plugins that need to run batched processes.

## Admin interface
![screenshot](screenshot.png)
