<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Entity\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;


/**
 * Job repository
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobRepository extends EntityRepository implements JobRepositoryInterface
{
    /**
     * Returns all active  jobs
     *
     * @return array
     */
    public function findActiveJobs()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->Where($qb->expr()->eq('p.active', $qb->expr()->literal(true)));
        $qb->orderBy('p.priority', 'ASC');
        $q = $qb->getQuery();

        return $q->execute();
    }

} 