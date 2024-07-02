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
use Sylius\Behat\Element\Admin\Crud\Index\SearchFilterElementInterface;

final class SearchFilterContext implements Context
{
    public function __construct(
        private SearchFilterElementInterface $searchFilterElement,
    ) {
    }

    /**
     * @When /^I search for [^"]+ with "([^"]+)"$/
     * @When /^I search for [^"]+ by "([^"]+)"$/
     * @When /^I search by "([^"]+)" [^"]+$/
     */
    public function iSearchResourceWith(string $phrase): void
    {
        $this->searchFilterElement->searchWith($phrase);
    }
}
