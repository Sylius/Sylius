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
    public function requireShippingAddressInCheckout(): void
    {
        $this->getElement('shipping_address_in_checkout_required')->check();
    }

    public function requireBillingAddressInCheckout(): void
    {
        $this->getElement('shipping_address_in_checkout_required')->uncheck();
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
        return $this->getElement('shipping_address_in_checkout_required')->isChecked();
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
            'shipping_address_in_checkout_required' => '#sylius_channel_shippingAddressInCheckoutRequired',
        ]);
    }
}
