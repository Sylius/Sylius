<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class GeneratePage extends SymfonyPage implements GeneratePageInterface
{
    public function generate()
    {
        $this->getDocument()->pressButton('Generate');
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPrice($nth, $price, $channelName)
    {
        $this->getElement('price', ['%position%' => $nth, '%channelName%' => $channelName])->setValue($price);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCode($nth, $code)
    {
        $this->getDocument()->fillField(sprintf('sylius_product_generate_variants_variants_%s_code', $nth), $code);
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariant($nth)
    {
        $item = $this->getDocument()->find('css', sprintf('div[data-form-collection-index="%s"]', $nth));

        $item->clickLink('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_product_variant_generate';
    }

    /**
     * {@inheritdoc}
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessage($element, $position)
    {
        $foundElement = $this->getElement($element, ['%position%' => $position]);
        $validatedField = $this->getValidatedField($foundElement);
        if (null === $validatedField) {
            throw new ElementNotFoundException($this->getSession(), 'Element', 'css', $foundElement);
        }

        return $validatedField->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getPricesValidationMessage($position)
    {
        return $this->getElement('prices_validation_message', ['%position%' => $position])->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_generate_variants_variants_%position%_code',
            'price' => '#sylius_product_generate_variants_variants_%position%_channelPricings > .field:contains("%channelName%") input',
            'prices_validation_message' => '#sylius_product_generate_variants_variants_%position%_channelPricings ~ .sylius-validation-error',
        ]);
    }

    /**
     * @param NodeElement $element
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    private function getValidatedField(NodeElement $element)
    {
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
