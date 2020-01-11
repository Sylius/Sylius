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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Taxon\CreatePage;

final class ManagingTranslatableEntitiesContext implements Context
{
    /** @var CreatePage */
    private $taxonCreatePage;

    public function __construct(CreatePage $taxonCreatePage)
    {
        $this->taxonCreatePage = $taxonCreatePage;
    }

    /**
     * @When I want to create a new translatable entity
     */
    public function iWantToCreateANewTranslatableEntity()
    {
        $this->taxonCreatePage->open();
    }

    /**
     * @Then I should be able to translate it in :localeCode
     */
    public function iShouldBeAbleToTranslateItIn($localeCode)
    {
        $this->taxonCreatePage->describeItAs('Foo bar', $localeCode);
    }
}
