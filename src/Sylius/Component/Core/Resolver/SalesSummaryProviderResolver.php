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

namespace Sylius\Component\Core\Resolver;

use Doctrine\DBAL\Connection;
use Sylius\Component\Core\Provider\SalesSummaryProviderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class SalesSummaryProviderResolver
{
    /** @var ServiceLocator */
    private $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function getSalesSummaryProvider(Connection $connection): SalesSummaryProviderInterface
    {
        $databaseName = $connection->getDatabasePlatform()->getName();

        if (!$this->locator->has($databaseName)) {
            return $this->locator->get('unHandled');
        }

        return $this->locator->get($databaseName);
    }
}
