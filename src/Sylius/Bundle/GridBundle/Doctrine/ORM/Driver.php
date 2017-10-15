<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class Driver implements DriverInterface
{
    public const NAME = 'doctrine/orm';

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        $repository = $this->managerRegistry
            ->getManagerForClass($configuration['class'])
            ->getRepository($configuration['class']);

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
