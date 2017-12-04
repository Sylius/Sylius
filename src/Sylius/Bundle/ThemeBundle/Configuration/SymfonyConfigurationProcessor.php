<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

final class SymfonyConfigurationProcessor implements ConfigurationProcessorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @param ConfigurationInterface $configuration
     * @param Processor $processor
     */
    public function __construct(ConfigurationInterface $configuration, Processor $processor)
    {
        $this->configuration = $configuration;
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $configs): array
    {
        return $this->processor->processConfiguration($this->configuration, $configs);
    }
}
