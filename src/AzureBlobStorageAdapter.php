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
     * @param string|null $publicEndpoint
     * @return void
     */
    public function setPublicEndpoint(?string $publicEndpoint): void
    {
        $this->publicEndpoint = $publicEndpoint;
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
        if ($this->publicEndpoint) {
            return sprintf('%s/%s/%s', $this->publicEndpoint, $this->container, $path);
        }

        return $this->client->getBlobUrl($this->container, $path);
    }
}
