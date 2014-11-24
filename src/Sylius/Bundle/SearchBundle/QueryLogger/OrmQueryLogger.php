<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\QueryLogger;

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\SearchBundle\Model\SearchLog;

/**
 * @author agounaris <agounaris@gmail.com>
 */
class OrmQueryLogger implements QueryLoggerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     */
    private $isEnabled;

    /**
     * @param EntityManager $em
     * @param               $isEnabled
     */
    public function __construct(EntityManager $em, $isEnabled)
    {
        $this->em = $em;
        $this->isEnabled = $isEnabled;
    }

    /**
     * @param $searchTerm
     * @param $ipAddress
     */
    public function logStringQuery($searchTerm, $ipAddress)
    {
        $searchLog = new SearchLog();

        $searchLog->setSearchString($searchTerm);
        $searchLog->setRemoteAddress($ipAddress);

        $this->em->persist($searchLog);
        $this->em->flush();
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }
}