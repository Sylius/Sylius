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

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Mink;
use Behat\Step\Then;
use Behat\Step\When;
use Sylius\Behat\Element\BrowserElementInterface;
use Webmozart\Assert\Assert;

final class BrowserContext implements Context
{
    private ?Mink $mink = null;

    public function __construct(private BrowserElementInterface $browserElement)
    {
    }

    #[BeforeScenarioScope]
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();
        if ($environment->hasContextClass(MinkContext::class)) {
            $minkContext = $environment->getContext(MinkContext::class);
            $this->mink = $minkContext->getMink();
        }
    }

    #[When('I go back one page in the browser')]
    public function iGoBackOnePageInTheBrowser(): void
    {
        $this->browserElement->goBack();
    }

    #[When('I switch to the second browser session')]
    public function iSwitchToTheSecondBrowserSession(): void
    {
        if ($this->mink !== null) {
            $this->mink->getSession()->pause();
            $this->mink->getSession('workerphp_second')->resume();
        }
    }

    #[When('I switch to the first browser session')]
    public function iSwitchToTheFirstBrowserSession(): void
    {
        if ($this->mink !== null) {
            $this->mink->getSession('workerphp_second')->pause();
            $this->mink->getSession()->resume();
        }
    }

}
