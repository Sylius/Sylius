<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Fixture;

use Symfony\Component\Config\Definition\Processor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureOptionsProcessor implements FixtureOptionsProcessorInterface
{
    /**
     * @var Processor
     */
    private $configurationProcessor;

    /**
     * @param Processor $configurationProcessor
     */
    public function __construct(Processor $configurationProcessor)
    {
        $this->configurationProcessor = $configurationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FixtureInterface $fixture, array $options)
    {
        return $this->configurationProcessor->processConfiguration($fixture, $options);
    }
}
