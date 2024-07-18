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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\OrderItem\Adjustment;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class CollectionProviderSpec extends ObjectBehavior
{
    public function let(
        OrderItemRepositoryInterface $orderItemRepository,
        SectionProviderInterface $sectionProvider,
    ): void {
        $this->beConstructedWith($orderItemRepository, $sectionProvider);
    }

    function it_is_a_state_provider(): void
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    function it_returns_an_empty_array_when_uri_variables_have_no_id(
        OrderItemRepositoryInterface $orderItemRepository,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: AdjustmentInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $orderItemRepository->findOneByIdAndOrderTokenValue(Argument::cetera())->shouldNotBeCalled();

        $this->provide($operation, ['tokenValue' => '42'])->shouldReturn([]);
    }

    function it_returns_an_empty_array_when_uri_variables_have_no_token_value(
        OrderItemRepositoryInterface $orderItemRepository,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: AdjustmentInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $orderItemRepository->findOneByIdAndOrderTokenValue(Argument::cetera())->shouldNotBeCalled();

        $this->provide($operation, ['id' => 42])->shouldReturn([]);
    }

    function it_returns_an_empty_array_when_no_order_item_can_be_found(
        OrderItemRepositoryInterface $orderItemRepository,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: AdjustmentInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $orderItemRepository->findOneByIdAndOrderTokenValue(42, 'token')->willReturn(null);

        $this->provide($operation, ['id' => '42', 'tokenValue' => 'token'])->shouldReturn([]);
    }

    function it_returns_adjustments_recursively(
        OrderItemRepositoryInterface $orderItemRepository,
        SectionProviderInterface $sectionProvider,
        Request $request,
        Request $queryRequest,
        OrderItem $orderItem,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
    ): void {
        $operation = new GetCollection(class: AdjustmentInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $request->query = $queryRequest;
        $queryRequest->get('type')->willReturn('type');
        $adjustments = new ArrayCollection([
            $firstAdjustment->getWrappedObject(),
            $secondAdjustment->getWrappedObject(),
        ]);

        $orderItem->getAdjustmentsRecursively('type')->willReturn($adjustments);
        $orderItemRepository->findOneByIdAndOrderTokenValue(42, 'token')->willReturn($orderItem);

        $this->provide($operation, ['id' => '42', 'tokenValue' => 'token'], ['request' => $request])->shouldReturn($adjustments);
    }

    function it_throws_an_exception_when_operation_class_is_not_adjustment(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_get_collection(
        Operation $operation,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation->getClass()->willReturn(AdjustmentInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: AdjustmentInterface::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }
}
