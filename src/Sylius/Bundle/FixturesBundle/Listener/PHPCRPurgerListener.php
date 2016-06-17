<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Listener;

use Doctrine\Common\DataFixtures\Purger\PHPCRPurger;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PHPCRPurgerListener extends AbstractListener implements BeforeSuiteListenerInterface
{
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
    public function beforeSuite(SuiteEvent $suiteEvent, array $options)
    {
        foreach ($options['managers'] as $managerName) {
            /** @var DocumentManagerInterface $manager */
            $manager = $this->managerRegistry->getManager($managerName);

            $purger = new PHPCRPurger($manager);
            $purger->purge();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phpcr_purger';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNode
            ->children()
                ->arrayNode('managers')
                    ->defaultValue([null])
                    ->prototype('scalar')
        ;
    }
}
