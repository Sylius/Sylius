<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\MultiContainerExtension;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ScopeManipulator implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::BEFORE => ['enterScope', 128],
            ExampleTested::BEFORE => ['enterScope', 128],
            ScenarioTested::AFTER => ['leaveScope', -128],
            ExampleTested::AFTER => ['leaveScope', -128],
        ];
    }

    public function enterScope()
    {
        if (!$this->container->isScopeActive('scenario')) {
            $this->container->enterScope('scenario');
        }
    }

    public function leaveScope()
    {
        if ($this->container->isScopeActive('scenario')) {
            $this->container->leaveScope('scenario');
        }
    }
}
