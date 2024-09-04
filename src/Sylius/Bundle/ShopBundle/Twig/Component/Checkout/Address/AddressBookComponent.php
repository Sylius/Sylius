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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class AddressBookComponent
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(writable: true, onUpdated: 'addressUpdated')]
    public mixed $addressId = null;

    #[LiveProp]
    public string $field;

    public function __construct(
        private readonly CustomerContextInterface $customerContext,
    ) {
    }

    /** @return array<AddressInterface> */
    #[ExposeInTemplate(name: 'addresses')]
    public function getAddresses(): array
    {
        /** @var null|CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();
        return $customer?->getAddresses()->toArray() ?? [];
    }

    public function addressUpdated(): void
    {
        $this->emit('sylius:shop:address-updated', ['addressId' => $this->addressId, 'field' => $this->field]);
    }
}
