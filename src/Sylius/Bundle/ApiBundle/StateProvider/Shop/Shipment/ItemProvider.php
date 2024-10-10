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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Shipment;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ShipmentInterface> */
final readonly class ItemProvider implements ProviderInterface
{
    /** @param ShipmentRepositoryInterface<ShipmentInterface> $shipmentRepository */
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private UserContextInterface $userContext,
        private ShipmentRepositoryInterface $shipmentRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        Assert::true(is_a($operation->getClass(), ShipmentInterface::class, true));
        Assert::isInstanceOf($operation, Get::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        $user = $this->userContext->getUser();
        if (!$user instanceof ShopUserInterface) {
            return null;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $user->getCustomer();
        if ($customer === null) {
            return null;
        }

        return $this
            ->shipmentRepository
            ->findOneByCustomerAndOrderToken($uriVariables['shipmentId'], $customer, $uriVariables['tokenValue'])
        ;
    }
}
