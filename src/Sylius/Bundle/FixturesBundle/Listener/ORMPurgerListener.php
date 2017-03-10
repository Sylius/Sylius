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

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ORMPurgerListener extends AbstractListener implements BeforeSuiteListenerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var array
     */
    private static $purgeModes = [
        'delete' => ORMPurger::PURGE_MODE_DELETE,
        'truncate' => ORMPurger::PURGE_MODE_TRUNCATE,
    ];

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
            /** @var EntityManagerInterface $manager */
            $manager = $this->managerRegistry->getManager($managerName);

            $purger = new ORMPurger($manager, $options['exclude']);
            $purger->setPurgeMode(static::$purgeModes[$options['mode']]);
            $purger->purge();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orm_purger';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNodeBuilder = $optionsNode->children();
        
        $optionsNodeBuilder
            ->enumNode('mode')
                ->values(['delete', 'truncate'])
                ->defaultValue('delete')
        ;

        $optionsNodeBuilder
            ->arrayNode('managers')
                ->defaultValue([null])
                ->prototype('scalar')
        ;

        $optionsNodeBuilder
            ->arrayNode('exclude')
                ->defaultValue([])
                ->prototype('scalar')
        ;
    }
}
