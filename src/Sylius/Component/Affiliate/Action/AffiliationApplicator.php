<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Action;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

class AffiliationApplicator implements AffiliationApplicatorInterface
{
    protected $registry;
    protected $manager;

    public function __construct(ServiceRegistryInterface $registry, ObjectManager $manager)
    {
        $this->registry = $registry;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($subject, AffiliateInterface $affiliate, GoalInterface $goal)
    {
        foreach ($goal->getActions() as $action) {
            $this->registry
                ->get($action->getType())
                ->execute($subject, $action->getConfiguration(), $affiliate)
            ;

            $goal->incrementUsed();
        }

        $this->manager->persist($goal);
        $this->manager->flush();
    }
}
