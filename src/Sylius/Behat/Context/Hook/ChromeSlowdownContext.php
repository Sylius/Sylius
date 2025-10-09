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
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\JQueryHelper;

final class ChromeSlowdownContext implements Context, MinkAwareContext
{
    private Mink $mink;

    public function setMink(Mink $mink): void
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters): void
    {
    }

    /**
     * @BeforeStep
     */
    public function waitBeforeStep(BeforeStepScope $scope): void
    {
        $session = $this->mink->getSession();
        $driver = $session->getDriver();

        // Only apply delays for JavaScript drivers
        if (!DriverHelper::isJavascript($driver)) {
            return;
        }

        $text = $scope->getStep()->getText();

        $slowActions = [
            'click', 'press', 'fill', 'select', 'check', 'uncheck',
            'choose', 'add', 'specify', 'attach', 'submit', 'save',
        ];

        foreach ($slowActions as $action) {
            if (stripos($text, $action) !== false) {
                // Use existing helpers to wait for page and async actions
                DriverHelper::waitForPageToLoad($session);
                JQueryHelper::waitForAsynchronousActionsToFinish($session);

                // Additional small delay for animations
                usleep(300000); // 300ms

                break;
            }
        }
    }

    /**
     * @AfterStep
     */
    public function waitAfterStep(AfterStepScope $scope): void
    {
        $session = $this->mink->getSession();
        $driver = $session->getDriver();

        // Only apply delays for JavaScript drivers
        if (!DriverHelper::isJavascript($driver)) {
            return;
        }

        $text = $scope->getStep()->getText();

        // Always wait for page to load after each step
        DriverHelper::waitForPageToLoad($session);
        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        // After form submissions or major actions, wait longer
        if (stripos($text, 'submit') !== false ||
            stripos($text, 'save') !== false ||
            stripos($text, 'create') !== false ||
            stripos($text, 'update') !== false ||
            stripos($text, 'add') !== false ||
            stripos($text, 'delete') !== false) {
            // Additional wait for major actions
            sleep(1);
        }
    }
}