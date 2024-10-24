<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    public function __construct(private TestThemeConfigurationManagerInterface $testThemeConfigurationManager)
    {
    }

    /**
     * @BeforeScenario
     */
    public function purgeTestThemes()
    {
        $this->testThemeConfigurationManager->clear();
    }
}
