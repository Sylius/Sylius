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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\OrderItemUnit;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<OrderItemUnitInterface> */
final readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private UserContextInterface $userContext,
        private OrderItemUnitRepositoryInterface $orderItemUnitRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?OrderItemUnitInterface
    {
        Assert::true(is_a($operation->getClass(), OrderItemUnitInterface::class, true));
        Assert::isInstanceOf($operation, Get::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        $user = $this->userContext->getUser();
        /** @var CustomerInterface|null $customer */
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;
        if ($customer !== null) {
            return $this->orderItemUnitRepository->findOneByCustomer($uriVariables['id'], $customer);
        }

        return null;
    }
}
