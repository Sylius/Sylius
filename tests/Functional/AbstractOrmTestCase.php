<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;
use Sylius\Tests\Functional\Doctrine\Mock\ConnectionMock;
use Sylius\Tests\Functional\Doctrine\Mock\DriverMock;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

abstract class AbstractOrmTestCase extends TestCase
{
    protected function getTestOrmConfiguration(): Configuration
    {
        $config = new Configuration();

        $config->setProxyDir(__DIR__ . '/../Doctrine/Proxies');
        $config->setProxyNamespace('Sylius\Tests\Functional\Doctrine\Proxies');
        $config->setMetadataCache(new ArrayAdapter());
        $config->setQueryCache(new ArrayAdapter());
        $config->setMetadataDriverImpl(ORMSetup::createDefaultAnnotationDriver());

        return $config;
    }

    protected function getTestOrmConnection($config): Connection
    {
        return DriverManager::getConnection([
            'driverClass'  => DriverMock::class,
            'wrapperClass' => ConnectionMock::class,
            'user'=> 'sylius',
            'password' => 'sylius',
        ], $config);
    }
}
