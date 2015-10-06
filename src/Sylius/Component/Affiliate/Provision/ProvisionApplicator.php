<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Provision;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

class ProvisionApplicator implements ProvisionApplicatorInterface
{
    protected $registry;
    protected $manager;

    public function __construct(ServiceRegistryInterface $registry, ObjectManager $manager)
    {
        $this->registry   = $registry;
        $this->manager    = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($subject, AffiliateInterface $affiliate, GoalInterface $goal)
    {
        foreach ($goal->getProvisions() as $provision) {
            $this->registry
                ->get($provision->getType())
                ->execute($subject, $provision->getConfiguration(), $affiliate)
            ;

            $goal->incrementUsed();
        }

        $this->manager->persist($goal);
        $this->manager->flush();
    }
}
