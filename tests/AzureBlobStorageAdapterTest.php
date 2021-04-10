<?php

declare(strict_types=1);

namespace Tests;

use Blue32a\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class AzureBlobStorageAdapterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&AzureBlobStorageAdapter
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AzureBlobStorageAdapter::class);
    }

    /**
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(AzureBlobStorageAdapter::class);
    }

    /**
     * @return \Mockery\MockInterface&\Mockery\LegacyMockInterface&BlobRestProxy
     */
    protected function createBlobRestProxyMock()
    {
        return Mockery::mock(BlobRestProxy::class);
    }

    /**
     * @test
     */
    public function testSetPublicEndpoint(): void
    {
        $url = 'https://example.com';

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $publicEndpointRef = $targetRef->getProperty('publicEndpoint');
        $publicEndpointRef->setAccessible(true);

        $targetMock->setPublicEndpoint($url);
        $this->assertEquals($url, $publicEndpointRef->getValue($targetMock));

        $targetMock->setPublicEndpoint(null);
        $this->assertNull($publicEndpointRef->getValue($targetMock));
    }

    /**
     * @test
     */
    public function testGetUrl(): void
    {
        $path       = 'sample.txt';
        $container  = 'test';
        $url        = sprintf('https://example.com/%s/%s', $container, $path);
        $clientMock = $this->createBlobRestProxyMock();

        $adapter = new AzureBlobStorageAdapter($clientMock, $container);
        $adapter->setPublicEndpoint(null);

        $targetRef = $this->createTargetReflection();

        $containerRef = $targetRef->getProperty('container');
        $containerRef->setAccessible(true);

        $clientMock
            ->shouldReceive('getBlobUrl')
            ->once()
            ->with($containerRef->getValue($adapter), $path)
            ->andReturn($url);

        $this->assertEquals($url, $adapter->getUrl($path));
    }

    /**
     * @test
     */
    public function testGetUrlWithPublicEndpoint(): void
    {
        $path           = 'sample.txt';
        $container      = 'test';
        $publicEndpoint = 'https://public.example.com';
        $clientMock     = $this->createBlobRestProxyMock();

        $adapter = new AzureBlobStorageAdapter($clientMock, $container);
        $adapter->setPublicEndpoint($publicEndpoint);

        $clientMock
            ->shouldReceive('getBlobUrl')
            ->never();

        $this->assertEquals(
            $publicEndpoint . '/' . $container . '/' . $path,
            $adapter->getUrl($path)
        );
    }
}
