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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Page\Admin\Product\Common\ProductMediaTrait;
use Sylius\Behat\Page\Admin\Product\Common\ProductTranslationsTrait;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\Routing\RouterInterface;

class UpdateConfigurableProductPage extends BaseUpdatePage implements UpdateConfigurableProductPageInterface
{
    use ChecksCodeImmutability;
    use ConfigurableProductFormTrait;
    use ProductMediaTrait;
    use ProductTranslationsTrait;

    /**
     * @param array<array-key, string> $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);
    }

    public function saveChanges(): void
    {
        $this->waitForFormUpdate();

        parent::saveChanges();
    }

    public function isProductOptionChosen(string $option): bool
    {
        $optionElement = $this->getElement('options')->getParent();

        return AutocompleteHelper::isValueVisible($this->getSession(), $optionElement, $option);
    }

    public function isProductOptionsDisabled(): bool
    {
        return 'disabled' === $this->getElement('options')->getAttribute('disabled');
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

        $this->getDriver()->executeScript(sprintf('$(\'input.search\').val(\'%s\')', $taxon->getName()));
        $this->getElement('search')->click();
        $this->getElement('search')->waitFor(10, fn () => $this->hasElement('search_item_selected'));
        $itemSelected = $this->getElement('search_item_selected');
        $itemSelected->click();
    }

    public function checkChannel(string $channelCode): void
    {
        $this->getElement('channel', ['%channel_code%' => $channelCode])->check();
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

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'options' => '[data-test-options]',
            ],
            $this->getDefinedFormElements(),
            $this->getDefinedProductMediaElements(),
            $this->getDefinedProductTranslationsElements(),
        );
    }

    private function openTaxonBookmarks(): void
    {
        $this->getElement('taxonomy')->click();
    }
}
