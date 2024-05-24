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

namespace Sylius\Behat\Page\Admin\Product\SimpleProduct;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Routing\RouterInterface;

class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    use SimpleProductFormTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function getRouteName(): string
    {
        return parent::getRouteName() . '_simple';
    }

    public function create(): void
    {
        $this->waitForFormUpdate();

        parent::create();
    }

    public function specifySlugIn(?string $slug, string $locale): void
    {
        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
    }

    public function addNonTranslatableAttribute(string $attributeName, string $value): void
    {
        $this->clickTabIfItsNotActive('attributes');

        $attributeOption = $this->getElement('attributes_choice')->find('css', sprintf('option:contains("%s")', $attributeName));
        $this->selectElementFromAttributesDropdown($attributeOption->getAttribute('value'));

        $this->getDocument()->pressButton('Add attributes');
        $this->waitForFormElement();

        $this->getElement('non_translatable_attribute_value', ['%attributeName%' => $attributeName])->setValue($value);
    }

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string
    {
        $this->clickTabIfItsNotActive('attributes');

        $validationError = $this->getElement('attribute')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $mainTaxonElement, $taxon->getName());
    }

    public function hasMainTaxonWithName(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return $taxonName === $mainTaxonElement->find('css', '.search > .text')->getText();
    }

    public function checkProductTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }
        $productTaxonsCodes[] = $taxon->getCode();

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function choosePricingCalculator(string $name): void
    {
        $this->getElement('price_calculator')->selectOption($name);
    }

    public function checkChannel(string $channelCode): void
    {
        $this->getElement('channel', ['%channel_code%' => $channelCode])->check();
    }

    public function activateLanguageTab(string $locale): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale%' => $locale]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }

    public function getChannelPricingValidationMessage(): string
    {
        return $this->getElement('prices_validation_message')->getText();
    }

    public function cancelChanges(): void
    {
        $this->getElement('cancel_button')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'attribute_value' => '#attributesContainer [data-test-product-attribute-value-in-locale="%attributeName% %localeCode%"] input',
                'images' => '#sylius_product_images',
            ],
            $this->getDefinedFormElements(),
        );
    }

    private function openTaxonBookmarks(): void
    {
        $this->getElement('taxonomy')->click();
    }

    private function selectElementFromAttributesDropdown(string $id): void
    {
        $this->getDriver()->executeScript('$(\'#sylius_product_attribute_choice\').dropdown(\'show\');');
        $this->getDriver()->executeScript(sprintf('$(\'#sylius_product_attribute_choice\').dropdown(\'set selected\', \'%s\');', $id));
    }

    private function waitForFormElement(int $timeout = 5): void
    {
        $form = $this->getElement('form');
        $this->getDocument()->waitFor($timeout, fn () => !str_contains($form->getAttribute('class'), 'loading'));
    }

    private function clickTabIfItsNotActive(string $tabName): void
    {
        $attributesTab = $this->getElement('tab', ['%name%' => $tabName]);
        if (!$attributesTab->hasClass('active')) {
            $attributesTab->click();
        }
    }
}
