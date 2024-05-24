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

namespace Sylius\Behat\Page\Admin\Product\ConfigurableProduct;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Page\Admin\Product\FormTrait;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class CreateConfigurableProductPage extends BaseCreatePage implements CreateConfigurableProductPageInterface
{
    use SpecifiesItsField;
    use FormTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function create(): void
    {
        $this->waitForFormUpdate();

        parent::create();
    }

    public function hasMainTaxonWithName(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return $taxonName === $mainTaxonElement->find('css', '.search > .text')->getText();
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $mainTaxonElement, $taxon->getName());
    }

    public function activateLanguageTab(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%localeCode%' => $localeCode]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string
    {
        $this->clickTabIfItsNotActive('attributes');

        $validationError = $this->getElement('attribute')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'attribute' => '.attribute',
                'images' => '#sylius_product_images',
                'language_tab' => '[data-locale="%localeCode%"] .title',
                'main_taxon' => '#sylius_product_mainTaxon',
                'options_choice' => '#sylius_product_options',
                'search' => '.ui.fluid.search.selection.dropdown',
                'search_item_selected' => 'div.menu > div.item.selected',
                'tab' => '.menu [data-tab="%name%"]',
                'taxonomy' => 'a[data-tab="taxonomy"]',
            ],
            $this->getDefinedFormElements(),
        );
    }

    private function openTaxonBookmarks(): void
    {
        $this->getElement('taxonomy')->click();
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }

    private function getLastImageElement(): NodeElement
    {
        $images = $this->getElement('images');
        $items = $images->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
