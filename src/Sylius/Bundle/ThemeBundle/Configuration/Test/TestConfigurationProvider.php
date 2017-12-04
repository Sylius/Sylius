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

namespace Sylius\Bundle\ThemeBundle\Configuration\Test;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;

final class TestConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var TestThemeConfigurationManagerInterface
     */
    private $testThemeConfigurationManager;

    /**
     * @param TestThemeConfigurationManagerInterface $testThemeConfigurationManager
     */
    public function __construct(TestThemeConfigurationManagerInterface $testThemeConfigurationManager)
    {
        $this->testThemeConfigurationManager = $testThemeConfigurationManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurations(): array
    {
        return $this->testThemeConfigurationManager->findAll();
    }
}
