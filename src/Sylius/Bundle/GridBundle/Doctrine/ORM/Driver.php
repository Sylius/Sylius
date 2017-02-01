<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Driver implements DriverInterface
{
    const NAME = 'doctrine/orm';

    /**
     * @var RegistryInterface
     */
    private $metadataRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $repositoryRegistry;

    /**
     * @param RegistryInterface $metadataRegistry
     * @param ServiceRegistryInterface $repositoryRegistry
     */
    public function __construct(RegistryInterface $metadataRegistry, ServiceRegistryInterface $repositoryRegistry)
    {
        $this->metadataRegistry = $metadataRegistry;
        $this->repositoryRegistry = $repositoryRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters)
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        $metadata = $this->metadataRegistry->getByClass($configuration['class']);
        $repository = $this->repositoryRegistry->get($metadata->getAlias());

        if (isset($configuration['repository']['method'])) {
            $method = $configuration['repository']['method'];
            $arguments = isset($configuration['repository']['arguments']) ? array_values($configuration['repository']['arguments']) : [];

            $queryBuilder = $repository->$method(...$arguments);
        } else {
            $queryBuilder = $repository->createQueryBuilder('o');
        }

        return new DataSource($queryBuilder);
    }
}
