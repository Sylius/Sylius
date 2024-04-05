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

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class ShippingAddressInCheckoutRequiredElement extends Element implements ShippingAddressInCheckoutRequiredElementInterface
{
    private const BILLING_RADIO_VALUE = '0';
    private const SHIPPING_RADIO_VALUE = '1';

    public function requireShippingAddressInCheckout(): void
    {
        $this->getElement('shipping_address_in_checkout_required')->selectOption(self::SHIPPING_RADIO_VALUE);
    }

    public function requireBillingAddressInCheckout(): void
    {
        $this->getElement('shipping_address_in_checkout_required')->selectOption(self::BILLING_RADIO_VALUE);
    }

    public function requireAddressTypeInCheckout(string $type): void
    {
        if ($type === 'shipping') {
            $this->requireShippingAddressInCheckout();

            return;
        }

        $this->requireBillingAddressInCheckout();
    }

    public function isShippingAddressInCheckoutRequired(): bool
    {
        return $this->getElement('shipping_address_in_checkout_required')->getValue() == self::SHIPPING_RADIO_VALUE;
    }

    public function getRequiredAddressTypeInCheckout(): string
    {
        if ($this->isShippingAddressInCheckoutRequired()) {
            return 'shipping';
        }

        return 'billing';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'shipping_address_in_checkout_required' => '[name="sylius_channel[shippingAddressInCheckoutRequired]"]',
        ]);
    }
}
