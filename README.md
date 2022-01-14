# The Symfony bundle for concurrent downloading files by Guzzle

## Installation

```bash
composer require amarkhai/parallel-downloader-bundle
```

## Usage

For downloading run method "download" with an array<string, string>, where value is the link for file which you need
to download and key is the name which will be used for saving the file: 

```php
    public function __construct(DownloadManager $downloadManager)
    {
        $this->downloadManager = $downloadManager;
    }
    
    public function foo()
    {
        $this->downloadManager->download([
            'filename1' => 'https://mysite.com/test1.png',
            'filename2' => 'https://mysite.com/test2.png',
            'filename3' => 'https://mysite.com/test3.png',
        ]);
    }
```

## Additional parameters

The method download has 2 additional parameters:
 - subFolder - the name of folder inside download folder (see below) where files from the list will be saved
 - options - Array of Guzzle request options to apply to each request.

## Configuration

It's possible to change some parameters in configuration files:
 - Folder for saving files
 - Quantity of retries after failure downloadings
 - Maximum number of requests to send concurrently

Here are default values for these parameters:

```yaml
# config/packages/amarkhai_parallel_downloader.yaml
parameters:
  amarkhai_parallel_downloader.download_files_folder: '%kernel.project_dir%/var/downloads'
  amarkhai_parallel_downloader.download_retry: 3
  amarkhai_parallel_downloader.download_concurrency: 10
```