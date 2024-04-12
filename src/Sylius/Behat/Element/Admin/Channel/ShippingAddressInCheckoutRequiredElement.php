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

namespace Sylius\Behat\Element\Admin\Channel;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Webmozart\Assert\Assert;

final class ShippingAddressInCheckoutRequiredElement extends Element implements ShippingAddressInCheckoutRequiredElementInterface
{
    private const ADDRESS_TYPE_BILLING = 'billing';
    private const ADDRESS_TYPE_SHIPPING = 'shipping';

    public function requireShippingAddressInCheckout(): void
    {
        $this->requireAddressTypeInCheckout(self::ADDRESS_TYPE_SHIPPING);
    }

    public function requireBillingAddressInCheckout(): void
    {
        $this->requireAddressTypeInCheckout(self::ADDRESS_TYPE_BILLING);
    }

    public function requireAddressTypeInCheckout(string $type): void
    {
        $this->getChoiceForAddressType($type)->click();
    }

    public function isShippingAddressInCheckoutRequired(): bool
    {
        return self::ADDRESS_TYPE_SHIPPING === $this->getRequiredAddressTypeInCheckout();
    }

    public function getRequiredAddressTypeInCheckout(): string
    {
        foreach ($this->getChoices() as $type => $choice) {
            if ($choice->isChecked()) {
                return $type;
            }
        }

        throw new \InvalidArgumentException('No address type selected.');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'shipping_address_in_checkout_required' => '[data-test-shipping-address-in-checkout-required]',
        ]);
    }

    private function getChoiceForAddressType(string $type): NodeElement
    {
        $choices = $this->getChoices();
        Assert::keyExists($choices, $type);

        return $choices[$type];
    }

    /** @return array<string, NodeElement> */
    private function getChoices(): array
    {
        $element = $this->getElement('shipping_address_in_checkout_required');
        $labelsElements = $element->findAll('css', 'label');

        $choices = [];
        foreach ($labelsElements as $labelElement) {
            $label = strtolower($labelElement->getText());
            foreach ([self::ADDRESS_TYPE_BILLING, self::ADDRESS_TYPE_SHIPPING] as $type) {
                if (str_contains($label, $type)) {
                    $choices[$type] = $element->findById($labelElement->getAttribute('for'));

                    continue 2;
                }
            }
        }

        return $choices;
    }
}
