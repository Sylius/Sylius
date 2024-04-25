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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Order;

use Sylius\Component\Addressing\Model\AddressLogEntry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(
    name: 'sylius_admin:order:history:address',
    template: '@SyliusAdmin/Order/history/content/sections/addresses/address.html.twig',
)]
final class AddressHistoryComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public ?string $addressId = null;

    #[LiveProp]
    public ?string $header = null;

    #[LiveProp]
    public ?string $sort = 'desc';

    /** @param RepositoryInterface<AddressLogEntry> $addressLogRepository */
    public function __construct(
        private readonly RepositoryInterface $addressLogRepository,
    ) {
    }

    /** @return AddressLogEntry[] */
    #[ExposeInTemplate]
    public function getAddressLogs(): array
    {
        return $this->addressLogRepository->findBy(
            ['objectId' => $this->addressId],
            ['loggedAt' => $this->sort],
        );
    }
}
