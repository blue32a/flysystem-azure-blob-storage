<?php

namespace Tests;

use Blue32a\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AzureBlobStorageAdapterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public const CONTAINER_NAME = 'test';

    /**
     * @test
     */
    public function testGetUrl()
    {
        $connection = 'DefaultEndpointsProtocol=https;AccountName=example;AccountKey=examplekey';
        $client = BlobRestProxy::createBlobService($connection);
        $path = 'dir';
        $url = 'https://example.com/dir/test.txt';
        $clientMock = Mockery::mock($client);
        $adapter = new AzureBlobStorageAdapter($clientMock, self::CONTAINER_NAME);

        $refAdapter = new \ReflectionClass($adapter);
        $refPropertyContainer = $refAdapter->getProperty('container');
        $refPropertyContainer->setAccessible(true);

        $clientMock->shouldReceive('getBlobUrl')
            ->once()
            ->with($refPropertyContainer->getValue($adapter), $path)
            ->andReturn($url)
            ->getMock();

        $this->assertEquals($url, $adapter->getUrl($path));
    }
}
