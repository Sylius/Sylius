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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ChannelInterface;

class GeneratePage extends SymfonyPage implements GeneratePageInterface
{
    public function generate(): void
    {
        $this->getDocument()->pressButton('Generate');
    }

    public function specifyPrice(int $nth, int $price, ChannelInterface $channel): void
    {
        $this->getElement('price', ['%position%' => $nth, '%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyCode(int $nth, string $code): void
    {
        $this->getDocument()->fillField(sprintf('sylius_product_generate_variants_variants_%s_code', $nth), $code);
    }

    public function removeVariant(int $nth): void
    {
        $item = $this->getDocument()->find('css', sprintf('div[data-form-collection-index="%s"]', $nth));

        $item->clickLink('Delete');
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_product_variant_generate';
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessage(string $element, int $position): string
    {
        $foundElement = $this->getElement($element, ['%position%' => $position]);
        $validatedField = $this->getValidatedField($foundElement);

        return $validatedField->find('css', '.sylius-validation-error')->getText();
    }

    public function getPricesValidationMessage(int $position): string
    {
        return $this->getElement('prices_validation_message', ['%position%' => $position])->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_generate_variants_variants_%position%_code',
            'price' => '#sylius_product_generate_variants_variants_%position%_channelPricings_%channelCode% input[id*="%channelCode%"]',
            'prices_validation_message' => 'div[data-form-collection-index="%position%"] div.tabular.menu ~ .sylius-validation-error',
        ]);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getValidatedField(NodeElement $element): NodeElement
    {
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }

    public function isGenerationPossible(): bool
    {
        $generateButton = $this->getDocument()->find('css', 'button:contains("Generate")');

        return !$generateButton->hasAttribute('disabled');
    }
}
