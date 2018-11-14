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

namespace Sylius\Bundle\GridBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

final class Driver implements DriverInterface
{
    public const NAME = 'doctrine/dbal';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters): DataSourceInterface
    {
        if (!array_key_exists('table', $configuration)) {
            throw new \InvalidArgumentException('"table" must be configured.');
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('o.*')
            ->from($configuration['table'], 'o')
        ;

        foreach ($configuration['aliases'] as $column => $alias) {
            $queryBuilder->addSelect(sprintf('o.%s as %s', $column, $alias));
        }

        return new DataSource($queryBuilder);
    }
}
