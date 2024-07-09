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
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Element\Admin\Taxon\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingTranslatableEntitiesContext implements Context
{
    public function __construct(
        private CreatePageInterface $taxonCreatePage,
        private FormElementInterface $taxonFormElement,
    ) {
    }

    /**
     * @When I want to create a new translatable entity
     */
    public function iWantToCreateANewTranslatableEntity(): void
    {
        $this->taxonCreatePage->open();
    }

    /**
     * @Then I should be able to translate it in :localeCode
     */
    public function iShouldBeAbleToTranslateItIn(string $localeCode): void
    {
        $this->taxonFormElement->describeItAs('Description', $localeCode);
    }

    /**
     * @Then I should not be able to translate it in :localeCode
     */
    public function iShouldNotBeAbleToTranslateItIn(string $localeCode): void
    {
        Assert::throws(
            fn () => $this->taxonFormElement->describeItAs('Description', $localeCode),
            ElementNotFoundException::class
        );
    }
}
