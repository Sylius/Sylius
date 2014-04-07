<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SequenceBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Sequence\Repository\SequenceRepositoryInterface;

/**
 * Repository for Sequence
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SequenceRepository extends EntityRepository implements SequenceRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLastIndex($type)
    {
        return (int) $this->getQueryBuilder()
            ->select($this->getAlias().'.index')
            ->where($this->getAlias().'.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementIndex($type)
    {
        return $this->getQueryBuilder()
            ->update($this->_entityName, 's')
            ->set('s.index', 's.index + 1')
            ->where('s.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 's';
    }
}
