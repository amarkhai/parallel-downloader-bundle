<?php

namespace Amarkhai\ParallelDownloaderBundle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;

/**
 * Мненеджер для параллельного скачивания файлов
 */
class DownloadManager
{
    private Client $httpClient;
    /**
     * Список URL, по которым не удалось скачать файлы
     * @var array<string, string>
     */
    private array $rejectedURLs = [];
    /**
     * Папка, куда будут сохраняться скачанные файлы
     */
    private string $downloadFolder;
    /**
     * Кол-во повторов при ошибках во время загрузки
     */
    private int $retry;
    /**
     * Максимальное кол-во запросов для одновременной отправки
     */
    private int $concurrency;

    public function __construct(
        Client $httpClient,
        string $downloadFolder,
        int $retry = 3,
        int $concurrency = 10
    ) {
        $this->httpClient = $httpClient;
        $this->downloadFolder = $downloadFolder;
        $this->retry = $retry;
        $this->concurrency = $concurrency;
    }

    /**
     * @param array<string, string> $urls Список URL файлов (ключи - имена файлов, которые будут созданы при
     *  сохранении), которые нужно скачать
     * @param array $options массив опций для запросов Guzzle, которые будут применены к каждому из запросов
     *
     * @throws \Throwable
     */
    public function download(array $urls, ?string $subFolder = null, array $options = []): bool
    {
        $targetFolder = $this->downloadFolder;
        if (!is_null($subFolder)) {
            $targetFolder = $this->downloadFolder . '/' . $subFolder;
        }
        $pool = new Pool($this->httpClient, $this->getRequestGenerator($urls, $targetFolder), [
            'concurrency' => $this->concurrency,
            'rejected' => function (\Exception $reason, $index) use ($urls) {
                if (!$reason instanceof RequestException) {
                    throw $reason;
                }
                $fileName = array_search($reason->getRequest()->getUri(), $urls);
                if ($fileName === false) {
                    throw new \Exception(sprintf(
                        'There are no URI=%s in the list',
                        $reason->getRequest()->getUri()
                    ));
                }
                $this->rejectedURLs[$fileName] = $reason->getRequest()->getUri();
            },
            'options' => $options,
        ]);

        $promise = $pool->promise();
        $promise->wait();

        if (count($this->rejectedURLs) > 0) {
            if ($this->retry === 0) {
                return false;
            } else {
                $urls = $this->rejectedURLs;
                --$this->retry;
                $this->rejectedURLs = [];
                return $this->download($urls);
            }
        }

        return true;
    }

    /**
     * @param array<string, string> $urls
     */
    private function getRequestGenerator(array $urls, string $folder): \Generator
    {
        foreach ($urls as $fileName => $url) {
            yield function() use ($url, $folder, $fileName) {
                return $this->httpClient->getAsync($url, [
                    'sink' => $folder . '/' . $fileName
                ]);
            };
        }
    }
}