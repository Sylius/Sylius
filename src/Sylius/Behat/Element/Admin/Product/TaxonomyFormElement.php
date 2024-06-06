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

namespace Sylius\Behat\Element\Admin\Product;

use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonomyFormElement extends BaseFormElement implements TaxonomyFormElementInterface
{
    public function __construct(
        Session $session,
        array|MinkParameters $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function selectMainTaxon(string $taxonName): void
    {
        $this->changeTab();

        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('main_taxon')->getXpath(),
            $taxonName,
        );
        $this->waitForFormUpdate();
    }

    public function getMainTaxon(): string
    {
        $this->changeTab();

        return $this->getElement('selected_main_taxon')->getText();
    }

    public function checkProductTaxon(TaxonInterface $taxon): void
    {
        $this->changeTab();

        $this->getElement('product_taxons_checkbox', ['%code%' => $taxon->getCode()])->check();
    }

    public function uncheckProductTaxon(TaxonInterface $taxon): void
    {
        $this->changeTab();

        $this->getElement('product_taxons_checkbox', ['%code%' => $taxon->getCode()])->uncheck();
    }

    public function isTaxonVisibleInMainTaxonList(string $taxonName): bool
    {
        $this->changeTab();

        $elements = $this->autocompleteHelper->search(
            $this->getDriver(),
            $this->getElement('main_taxon')->getXpath(),
            $taxonName,
        );

        foreach ($elements as $element) {
            if (str_contains($element, $taxonName)) {
                return true;
            }
        }

        return false;
    }

    public function isTaxonChosen(string $taxonCode): bool
    {
        $this->changeTab();

        return $this->getElement('product_taxons_checkbox', ['%code%' => $taxonCode])->isChecked();
    }

    protected function getDefinedElements(): array
    {
        return [
            'form' => '[data-live-name-value="sylius_admin:product:form"]',
            'main_taxon' => '[data-test-main-taxon]',
            'product_taxons_checkbox' => '[data-test-product-taxons] [data-id="%code%"] input[type="checkbox"]',
            'selected_main_taxon' => '[data-test-main-taxon] option:selected',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ];
    }

    private function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'taxonomy'])->click();
    }
}
