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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\ErrorPageInterface;
use Webmozart\Assert\Assert;

final readonly class ErrorPageContext implements Context
{
    public function __construct(private ErrorPageInterface $errorPage)
    {
    }

    /**
     * @Then I should see the not found page with the link to the dashboard
     */
    public function iShouldSeeTheNotFoundPageWithTheLinkToTheDashboard(): void
    {
        Assert::true($this->errorPage->isItAdminNotFoundPage(), 'This test might require to be run without debug mode enabled.');
    }
}
