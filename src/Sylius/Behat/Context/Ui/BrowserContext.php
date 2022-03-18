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

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\BrowserElementInterface;

final class BrowserContext implements Context
{
    /** @var BrowserElementInterface */
    private $browserElement;

    public function __construct(BrowserElementInterface $browserElement)
    {
        $this->browserElement = $browserElement;
    }

    /**
     * @When I go back one page in the browser
     */
    public function iGoBackOnePageInTheBrowser(): void
    {
        $this->browserElement->goBack();
    }
}
