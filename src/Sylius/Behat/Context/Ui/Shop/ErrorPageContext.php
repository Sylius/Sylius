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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\ErrorPageInterface;
use Webmozart\Assert\Assert;

final class ErrorPageContext implements Context
{
    public function __construct(private ErrorPageInterface $errorPage)
    {
    }

    /**
     * @Then I should see the not found page
     */
    public function iShouldSeeTheNotFoundPage(): void
    {
        Assert::true($this->errorPage->isItShopNotFoundPage(), );
    }
}
