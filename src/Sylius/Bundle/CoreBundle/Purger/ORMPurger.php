<?php

namespace Sylius\Bundle\CoreBundle\Purger;

use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * ORMPurger that caches generated purge queries and uses them if called another time.
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ORMPurger implements PurgerInterface
{
    /**
     * @var PurgerInterface
     */
    private $purger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var QueryLogger
     */
    private $queryLogger;

    /**
     * @var array
     */
    private $cachedQueries = [];

    /**
     * @param PurgerInterface $purger
     * @param EntityManagerInterface $entityManager
     * @param QueryLogger $queryLogger
     */
    public function __construct(PurgerInterface $purger, EntityManagerInterface $entityManager, QueryLogger $queryLogger)
    {
        $this->purger = $purger;
        $this->entityManager = $entityManager;
        $this->queryLogger = $queryLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        if (empty($this->cachedQueries)) {
            $this->purgeForTheFirstTime();
            return;
        }

        $this->purgeUsingCachedQueries();
    }

    private function purgeForTheFirstTime()
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($this->queryLogger);

        $this->purger->purge();

        $this->cachedQueries = $this->queryLogger->getLoggedQueries();
        $this->queryLogger->clearLoggedQueries();
    }

    private function purgeUsingCachedQueries()
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        foreach ($this->cachedQueries as $cachedQuery) {
            $this->entityManager->getConnection()->executeUpdate(
                $cachedQuery['sql'],
                $cachedQuery['params'],
                $cachedQuery['types']
            );
        }
    }
}
