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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Page\Admin\Product\Common\ProductAssociationsTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductMediaTrait;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Routing\RouterInterface;

class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
{
    use ChecksCodeImmutability;
    use ProductAssociationsTrait;
    use ProductMediaTrait;
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

    public function saveChanges(): void
    {
        $this->waitForFormUpdate();

        parent::saveChanges();
    }

    public function specifyPrice(ChannelInterface $channel, string $price): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());

        $this->getElement('field_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalPrice(ChannelInterface $channel, string $originalPrice): void
    {
        $this->changeTab('channel-pricing');
        $this->changeChannelTab($channel->getCode());

        $this->getElement('field_original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function removeAttribute(string $attributeName, string $localeCode): void
    {
        $this->clickTabIfItsNotActive('attributes');

        $this->getElement('attribute_delete_button', ['%attributeName%' => $attributeName, '$localeCode%' => $localeCode])->press();
    }

    public function getAttributeSelectText(string $attribute, string $localeCode): string
    {
        $this->clickTabIfItsNotActive('attributes');

        return $this->getElement('attribute_select', ['%attributeName%' => $attribute, '%localeCode%' => $localeCode])->getText();
    }

    public function getNonTranslatableAttributeValue(string $attribute): string
    {
        $this->clickTabIfItsNotActive('attributes');

        return $this->getElement('non_translatable_attribute', ['%attributeName%' => $attribute])->getValue();
    }

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string
    {
        $this->clickTabIfItsNotActive('attributes');

        $validationError = $this->getElement('attribute_element')->find('css', '.sylius-validation-error');

        return $validationError->getText();
    }

    public function hasAttribute(string $attributeName): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('.attribute .label:contains("%s")', $attributeName));
    }

    public function hasNonTranslatableAttributeWithValue(string $attributeName, string $value): bool
    {
        $attribute = $this->getDocument()->find('css', sprintf('.attribute .attribute-label:contains("%s")', $attributeName));

        return
            $attribute->getParent()->getParent()->find('css', '.attribute-input input')->getValue() === $value &&
            $attribute->find('css', '.globe.icon') !== null
        ;
    }

    public function selectMainTaxon(TaxonInterface $taxon): void
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $mainTaxonElement, $taxon->getName());
    }

    public function isTaxonVisibleInMainTaxonList(string $taxonName): bool
    {
        $this->openTaxonBookmarks();

        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return AutocompleteHelper::isValueVisible($this->getSession(), $mainTaxonElement, $taxonName);
    }

    public function selectProductTaxon(TaxonInterface $taxon): void
    {
        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }
        $productTaxonsCodes[] = $taxon->getCode();

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function unselectProductTaxon(TaxonInterface $taxon): void
    {
        $productTaxonsCodes = [];
        $productTaxonsElement = $this->getElement('product_taxons');
        if ($productTaxonsElement->getValue() !== '') {
            $productTaxonsCodes = explode(',', $productTaxonsElement->getValue());
        }

        $key = array_search($taxon->getCode(), $productTaxonsCodes);
        if ($key !== false) {
            unset($productTaxonsCodes[$key]);
        }

        $productTaxonsElement->setValue(implode(',', $productTaxonsCodes));
    }

    public function hasMainTaxon(): bool
    {
        $this->openTaxonBookmarks();

        return $this->getDocument()->find('css', '.search > .text')->getText() !== '';
    }

    public function hasMainTaxonWithName(string $taxonName): bool
    {
        $this->openTaxonBookmarks();
        $mainTaxonElement = $this->getElement('main_taxon')->getParent();

        return $taxonName === $mainTaxonElement->find('css', '.search > .text')->getText();
    }

    public function isTaxonChosen(string $taxonName): bool
    {
        $productTaxonsElement = $this->getElement('product_taxons');

        $taxonName = strtolower(str_replace('-', '_', $taxonName));

        return str_contains($productTaxonsElement->getValue(), $taxonName);
    }

    public function disableTracking(): void
    {
        $this->getElement('tracked')->uncheck();
    }

    public function enableTracking(): void
    {
        $this->getElement('tracked')->check();
    }

    public function isTracked(): bool
    {
        return $this->getElement('tracked')->isChecked();
    }

    public function enableSlugModification(string $locale): void
    {
        SlugGenerationHelper::enableSlugModification(
            $this->getSession(),
            $this->getElement('toggle_slug_modification_button', ['%locale%' => $locale]),
        );
    }

    public function isSlugReadonlyIn(string $locale): bool
    {
        return SlugGenerationHelper::isSlugReadonly(
            $this->getSession(),
            $this->getElement('slug', ['%locale%' => $locale]),
        );
    }

    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string
    {
        $priceConfigurationElement = $this->getElement('pricing_configuration');
        $priceElement = $priceConfigurationElement
            ->find('css', sprintf('label:contains("%s %s")', $channel->getCode(), $currency->getCode()))->getParent();

        return $priceElement->find('css', 'input')->getValue();
    }

    public function getSlug(string $locale): string
    {
        return $this->getElement('slug', ['%locale%' => $locale])->getValue();
    }

    public function specifySlugIn(string $slug, string $locale): void
    {
        $this->activateLanguageTab($locale);

        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
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

    public function getPriceForChannel(ChannelInterface $channel): string
    {
        return $this->getElement('field_price', ['%channelCode%' => $channel->getCode()])->getValue();
    }

    public function getOriginalPriceForChannel(ChannelInterface $channel): string
    {
        return $this->getElement('field_original_price', ['%channelCode%' => $channel->getCode()])->getValue();
    }

    public function goToVariantsList(): void
    {
        $this->getDocument()->clickLink('List variants');
    }

    public function goToVariantCreation(): void
    {
        $this->getDocument()->clickLink('Create');
    }

    public function goToVariantGeneration(): void
    {
        $this->getDocument()->clickLink('Generate');
    }

    public function getShowProductInSingleChannelUrl(): string
    {
        return $this->getElement('show_product_button')->getAttribute('href');
    }

    public function isShowInShopButtonDisabled(): bool
    {
        return $this->getElement('show_product_button')->hasClass('disabled');
    }

    public function showProductInChannel(string $channel): void
    {
        $this->getElement('show_product_button')->clickLink($channel);
    }

    public function showProductInSingleChannel(): void
    {
        $this->getElement('show_product_button')->click();
    }

    public function disable(): void
    {
        $this->getElement('enabled')->uncheck();
    }

    public function isEnabled(): bool
    {
        return $this->getElement('enabled')->isChecked();
    }

    public function enable(): void
    {
        $this->getElement('enabled')->check();
    }

    public function hasNoPriceForChannel(string $channelName): bool
    {
        return !str_contains($this->getElement('prices')->getHtml(), $channelName);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'show_product_button' => '[data-test-view-in-store]',
            ],
            $this->getDefinedFormElements(),
            $this->getDefinedProductMediaElements(),
            $this->getDefinedProductAssociationsElements(),
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
}
