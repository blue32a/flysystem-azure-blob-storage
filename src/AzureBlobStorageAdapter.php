<?php

namespace Blue32a\Flysystem\AzureBlobStorage;

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter as BaseStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class AzureBlobStorageAdapter extends BaseStorageAdapter
{
    /** @var BlobRestProxy */
    private $client;

    /** @var string */
    private $container;

    /**
     * construct
     *
     * @param BlobRestProxy $client
     * @param string $container
     * @param string|null $prefix
     */
    public function __construct(BlobRestProxy $client, $container, $prefix = null)
    {
        $this->client = $client;
        $this->container = $container;

        parent::__construct($client, $container, $prefix);
    }

    /**
     * return URL
     *
     * @param string $path
     * @return string
     * @see \Illuminate\Filesystem\FilesystemAdapter::url()
     */
    public function getUrl($path)
    {
        return $this->client->getBlobUrl($this->container, $path);
    }
}
