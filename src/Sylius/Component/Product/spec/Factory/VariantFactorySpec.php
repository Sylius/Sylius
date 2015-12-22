<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Factory\VariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RepositoryInterface $promotionRepository)
    {
        $this->beConstructedWith($factory, $promotionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Factory\VariantFactory');
    }
    
    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }
    
    function it_implements_coupon_factory_interface()
    {
        $this->shouldImplement(VariantFactoryInterface::class);
    }

    function it_creates_new_coupon(FactoryInterface $factory, VariantInterface $coupon)
    {
        $factory->createNew()->willReturn($coupon);
        
        $this->createNew()->shouldReturn($coupon);
    }
    
    function it_throws_an_exception_when_promotion_is_not_found(RepositoryInterface $promotionRepository)
    {
        $promotionRepository->find(15)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForProduct', array(15))
        ;
    }

    function it_creates_a_coupon_and_assigns_a_promotion_to_id(
        FactoryInterface $factory,
        RepositoryInterface $promotionRepository,
        ProductInterface $promotion,
        VariantInterface $coupon
    )
    {
        $factory->createNew()->willReturn($coupon);
        $promotionRepository->find(13)->willReturn($promotion);
        $coupon->setProduct($promotion)->shouldBeCalled();

        $this->createForProduct(13)->shouldReturn($coupon);
    }
}
