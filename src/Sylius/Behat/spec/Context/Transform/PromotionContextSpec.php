<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Context\Transform\PromotionContext;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @mixin PromotionContext
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class PromotionContextSpec extends ObjectBehavior
{
    function let(PromotionRepositoryInterface $promotionRepository)
    {
        $this->beConstructedWith($promotionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\PromotionContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_gets_promotion_by_its_name(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion
    ) {
        $promotion->getName()->willReturn('Best Promotion');
        $promotionRepository->findOneBy(['name' => 'Best Promotion'])->willReturn($promotion);

        $this->getPromotionByName('Best Promotion');
    }

    function it_throws_exception_when_promotion_with_given_name_was_not_found(
        PromotionRepositoryInterface $promotionRepository
    ) {
        $promotionRepository->findOneBy(['name' => 'Wrong Promotion'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getPromotionByName', ['Wrong Promotion']);
    }
}
