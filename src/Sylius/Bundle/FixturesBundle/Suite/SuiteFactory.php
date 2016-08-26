<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Suite;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteFactory implements SuiteFactoryInterface
{
    /**
     * @var FixtureRegistryInterface
     */
    private $fixtureRegistry;

    /**
     * @var ListenerRegistryInterface
     */
    private $listenerRegistry;

    /**
     * @var Processor
     */
    private $optionsProcessor;

    /**
     * @param FixtureRegistryInterface $fixtureRegistry
     * @param ListenerRegistryInterface $listenerRegistry
     * @param Processor $optionsProcessor
     */
    public function __construct(
        FixtureRegistryInterface $fixtureRegistry,
        ListenerRegistryInterface $listenerRegistry,
        Processor $optionsProcessor
    ) {
        $this->fixtureRegistry = $fixtureRegistry;
        $this->listenerRegistry = $listenerRegistry;
        $this->optionsProcessor = $optionsProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function createSuite($name, array $configuration)
    {
        Assert::keyExists($configuration, 'fixtures');
        Assert::keyExists($configuration, 'listeners');

        $suite = new Suite($name);

        foreach ($configuration['fixtures'] as $fixtureAlias => $fixtureAttributes) {
            $this->addFixtureToSuite($suite, $fixtureAlias, $fixtureAttributes);
        }

        foreach ($configuration['listeners'] as $listenerName => $listenerAttributes) {
            $this->addListenerToSuite($suite, $listenerName, $listenerAttributes);
        }

        return $suite;
    }

    /**
     * @param Suite $suite
     * @param string $fixtureAlias
     * @param array $fixtureAttributes
     */
    private function addFixtureToSuite(Suite $suite, $fixtureAlias, array $fixtureAttributes)
    {
        Assert::keyExists($fixtureAttributes, 'name');
        Assert::keyExists($fixtureAttributes, 'options');

        $fixture = $this->fixtureRegistry->getFixture($fixtureAttributes['name']);
        $fixtureOptions = $this->optionsProcessor->processConfiguration($fixture, $fixtureAttributes['options']);
        $fixturePriority = isset($fixtureAttributes['priority']) ? $fixtureAttributes['priority'] : 0;

        $suite->addFixture($fixture, $fixtureOptions, $fixturePriority);
    }

    /**
     * @param Suite $suite
     * @param string $listenerName
     * @param array $listenerAttributes
     */
    private function addListenerToSuite(Suite $suite, $listenerName, array $listenerAttributes)
    {
        Assert::keyExists($listenerAttributes, 'options');

        $listener = $this->listenerRegistry->getListener($listenerName);
        $listenerOptions = $this->optionsProcessor->processConfiguration($listener, $listenerAttributes['options']);
        $listenerPriority = isset($listenerAttributes['priority']) ? $listenerAttributes['priority'] : 0;

        $suite->addListener($listener, $listenerOptions, $listenerPriority);
    }
}
