<?php

declare(strict_types=1);

namespace Blue32a\Flysystem\AzureBlobStorage;

use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter as BaseStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class AzureBlobStorageAdapter extends BaseStorageAdapter
{
    /** @var BlobRestProxy */
    protected $client;

    /** @var string */
    protected $container;

    /** @var string|null */
    protected $publicEndpoint;

    /**
     * @param BlobRestProxy $client
     * @param string        $container
     * @param string|null   $prefix
     */
    public function __construct(BlobRestProxy $client, $container, $prefix = null)
    {
        $this->client    = $client;
        $this->container = $container;

        parent::__construct($client, $container, $prefix);
    }

    public function setPublicEndpoint(?string $publicEndpoint): void
    {
        $this->publicEndpoint = $publicEndpoint;
    }

    /**
     * @see \Illuminate\Filesystem\FilesystemAdapter::url()
     *
     * @param string $path
     * @return string
     */
    public function getUrl($path)
    {
        if ($this->publicEndpoint) {
            return sprintf('%s/%s/%s', $this->publicEndpoint, $this->container, $path);
        }

        return $this->client->getBlobUrl($this->container, $path);
    }
}
