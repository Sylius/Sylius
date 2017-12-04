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

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;

final class TestThemeContext implements Context
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
     * @BeforeScenario
     */
    public function purgeTestThemes()
    {
        $this->testThemeConfigurationManager->clear();
    }
}
