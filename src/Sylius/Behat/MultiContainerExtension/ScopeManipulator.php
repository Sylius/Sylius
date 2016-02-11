<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sylius\Behat\MultiContainerExtension;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

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
