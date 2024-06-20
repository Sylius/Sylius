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
use Sylius\Behat\Service\TabsHelper;

class GeneratePage extends SymfonyPage implements GeneratePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_product_variant_generate';
    }

    public function specifyCode(int $nth, string $code): void
    {
        $this->getElement('code', ['%position%' => $nth])->setValue($code);
    }

    public function specifyPrice(int $nth, int $price, string $channelCode): void
    {
        $channelPricing = $this->getElement('channel_pricings', ['%position%' => $nth]);

        TabsHelper::switchTab($this->getSession(), $channelPricing, $channelCode);

        $channelPricing->find('css', sprintf('[id$="_channelPricings_%s"]', $channelCode))->fillField('Price', $price);
    }

    public function generate(): void
    {
        $this->getElement('generate_button')->press();
    }

    public function removeVariant(int $nth): void
    {
        $this->getElement('delete_button', ['%position%' => $nth])->click();
        $this->waitForFormUpdate();
    }

    public function getValidationMessage(string $element, int $position): string
    {
        $foundElement = $this->getFieldElement($element, ['%position%' => $position]);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    public function isGenerationPossible(): bool
    {
        return !$this->getElement('generate_button')->hasAttribute('disabled');
    }

    public function isProductVariantRemovable(int $nth): bool
    {
        return $this->hasElement('delete_button', ['%position%' => $nth]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channel_pricings' => '#sylius_admin_product_generate_variants_variants_%position% [data-test-channel-pricings]',
            'code' => '#sylius_admin_product_generate_variants_variants_%position% [data-test-code]',
            'delete_button' => '#sylius_admin_product_generate_variants_variants_%position% [data-test-delete-button]',
            'form' => 'form',
            'generate_button' => '[data-test-generate-button]',
        ]);
    }

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters = []): ?NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
