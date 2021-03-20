# BatchPress

BatchPress is a little starter plugin to help process data in batches. With BatchPress you can launch, monitor and cancel batched jobs.

### Usage

To use BatchPress just update the jobs.php file as follows:
1. Update the list of available jobs i.e. `$list = ['import' => 'Import Items']`.
2. Create the corresponding methods to handle the jobs you've added. i.e. `getImport()` and `processImport($item)`.

`Note:` BatchPress doesn't process anything out of the box, this is meant purely as a starting point for creating quick plugins that need to run batched processes.

## Admin interface
![screenshot](screenshot.png)
