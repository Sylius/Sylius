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

use Sylius\Bundle\FixturesBundle\Fixture\FixtureOptionsProcessorInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
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
     * @var FixtureOptionsProcessorInterface
     */
    private $fixtureOptionsProcessor;

    /**
     * @param FixtureRegistryInterface $fixtureRegistry
     * @param FixtureOptionsProcessorInterface $fixtureOptionsProcessor
     */
    public function __construct(
        FixtureRegistryInterface $fixtureRegistry,
        FixtureOptionsProcessorInterface $fixtureOptionsProcessor
    ) {
        $this->fixtureRegistry = $fixtureRegistry;
        $this->fixtureOptionsProcessor = $fixtureOptionsProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function createSuite($name, array $configuration)
    {
        Assert::keyExists($configuration, 'fixtures');

        $suite = new Suite($name);

        foreach ($configuration['fixtures'] as $fixtureName => $fixtureAttributes) {
            $this->addFixtureToSuite($suite, $fixtureName, $fixtureAttributes);
        }

        return $suite;
    }

    /**
     * @param Suite $suite
     * @param string $fixtureName
     * @param array $fixtureAttributes
     */
    private function addFixtureToSuite(Suite $suite, $fixtureName, array $fixtureAttributes)
    {
        Assert::keyExists($fixtureAttributes, 'options');

        $fixture = $this->fixtureRegistry->getFixture($fixtureName);
        $fixtureOptions = $this->fixtureOptionsProcessor->process($fixture, $fixtureAttributes['options']);
        $fixturePriority = isset($fixtureAttributes['priority']) ? $fixtureAttributes['priority'] : 0;

        $suite->addFixture($fixture, $fixtureOptions, $fixturePriority);
    }
}
