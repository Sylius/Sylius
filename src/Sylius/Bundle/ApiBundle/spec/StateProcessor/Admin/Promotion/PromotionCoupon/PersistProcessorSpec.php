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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolverInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $persistProcessor,
        UriTemplateParentResourceResolverInterface $uriTemplateParentResourceResolver,
        ValidatorInterface $validator,
    ): void {
        $this->beConstructedWith($persistProcessor, $uriTemplateParentResourceResolver, $validator);
    }

    function it_is_a_processor_interface(): void
    {
        $this->shouldImplement(ProcessorInterface::class);
    }

    function it_processes_a_promotion_coupon_if_operation_is_not_post(
        ProcessorInterface $persistProcessor,
        UriTemplateParentResourceResolverInterface $uriTemplateParentResourceResolver,
        ValidatorInterface $validator,
        Operation $operation,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $persistProcessor->process($promotionCoupon, $operation, [], [])->shouldBeCalled();

        $uriTemplateParentResourceResolver->resolve(Argument::cetera())->shouldNotBeCalled();
        $validator->validate(Argument::cetera())->shouldNotBeCalled();

        $this->process($promotionCoupon, $operation, [], []);
    }

    function it_processes_a_promotion_coupon_if_operation_is_post(
        ProcessorInterface $persistProcessor,
        UriTemplateParentResourceResolverInterface $uriTemplateParentResourceResolver,
        ValidatorInterface $validator,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $uriTemplateParentResourceResolver->resolve($promotionCoupon, $operation, [])->willReturn($promotion);

        $validator->validate($promotionCoupon, null, ['sylius'])->willReturn($constraintViolationList);
        $constraintViolationList->count()->willReturn(0);

        $persistProcessor->process($promotionCoupon, $operation, [], [])->shouldBeCalled();

        $this->process($promotionCoupon, $operation, [], []);
    }

    function it_throws_a_validation_exception_if_there_are_violations(
        ProcessorInterface $persistProcessor,
        UriTemplateParentResourceResolverInterface $uriTemplateParentResourceResolver,
        ValidatorInterface $validator,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        ConstraintViolationListInterface $constraintViolationList,
    ): void {
        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $uriTemplateParentResourceResolver->resolve($promotionCoupon, $operation, [])->willReturn($promotion);

        $validator->validate($promotionCoupon, null, ['sylius'])->willReturn($constraintViolationList);

        $constraintViolationList->count()->willReturn(1);
        $constraintViolationList->rewind()->shouldBeCalled();
        $constraintViolationList->valid()->willReturn(false);

        $persistProcessor->process($promotionCoupon, $operation, [], [])->shouldNotBeCalled();

        $this
            ->shouldThrow(ValidationException::class)
            ->during('process', [$promotionCoupon, $operation, [], []])
        ;
    }
}
