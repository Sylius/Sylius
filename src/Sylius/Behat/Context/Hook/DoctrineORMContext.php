<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeScenario;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\Platform\PlatformHelper;

final class DoctrineORMContext implements Context
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[BeforeScenario]
    public function purgeDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $configuration = $connection->getConfiguration();
        if (method_exists($configuration, 'setSQLLogger')) {
            $configuration->setSQLLogger(null);
        }

        $isMysql = PlatformHelper::isMysql($connection->getDatabasePlatform());
        if ($isMysql) {
            $connection->executeStatement('SET foreign_key_checks = 0');
        }

        try {
            $purger = new ORMPurger($this->entityManager);
            $purger->purge();
        } finally {
            if ($isMysql) {
                $connection->executeStatement('SET foreign_key_checks = 1');
            }
        }

        $this->entityManager->clear();
    }
}
