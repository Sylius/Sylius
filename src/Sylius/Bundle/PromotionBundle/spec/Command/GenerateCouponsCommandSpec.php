<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\PromotionBundle\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PromotionRepository;
use Sylius\Bundle\PromotionBundle\Command\GenerateCouponsCommand;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCouponsCommandSpec extends ObjectBehavior
{
    public function let(
        PromotionRepository $promotionRepository,
        PromotionCouponGeneratorInterface $couponGenerator
    ): void {
        $this->beConstructedWith($promotionRepository, $couponGenerator);
    }

    public function it_initializes(): void
    {
        $this->shouldImplement(GenerateCouponsCommand::class);
    }

    public function it_throws_an_error_if_the_promotion_does_not_exist(
        InputInterface $input,
        OutputInterface $output,
        PromotionRepository $promotionRepository,
        PromotionCouponGeneratorInterface $couponGenerator
    ): void {
        $input->getArgument('promotionCode')->willReturn('SOME_CODE');
        $promotionRepository->findOneBy(['code' => 'SOME_CODE'])->willReturn(null);

        $output->writeln(Argument::containingString('error'))->shouldBeCalled();
        $couponGenerator->generate(Argument::any())->shouldNotBeCalled();

        $this->execute($input, $output);
    }

    public function it_throws_an_error_if_the_promotion_is_not_coupon_based(
        InputInterface $input,
        OutputInterface $output,
        PromotionRepository $promotionRepository,
        PromotionInterface $promotion,
        PromotionCouponGeneratorInterface $couponGenerator
    ): void {
        $input->getArgument('promotionCode')->willReturn('SOME_CODE');
        $promotionRepository->findOneBy(['code' => 'SOME_CODE'])->willReturn($promotion);

        $promotion->isCouponBased()->willReturn(false);
        $output->writeln(Argument::containingString('error'))->shouldBeCalled();
        $couponGenerator->generate(Argument::any())->shouldNotBeCalled();

        $this->execute($input, $output);
    }

    public function it_creates_a_coupon_generator_instruction(
        InputInterface $input,
        OutputInterface $output,
        PromotionRepository $promotionRepository,
        PromotionInterface $promotion,
        PromotionCouponGeneratorInterface $couponGenerator
    ): void {
        $input->getArgument('promotionCode')->willReturn('SOME_CODE');
        $input->getArgument('count')->willReturn('10');
        $input->getArgument('length')->willReturn('20');
        $promotionRepository->findOneBy(['code' => 'SOME_CODE'])->willReturn($promotion);

        $promotion->isCouponBased()->willReturn(true);

        // Testing the instruction generation
        $expectedInstructions = new PromotionCouponGeneratorInstruction();
        $expectedInstructions->setAmount(10);
        $expectedInstructions->setCodeLength(20);

        $this->getGeneratorInstructions(10, 20)
            ->shouldBeLike($expectedInstructions);

        $couponGenerator->generate(
            $promotion,
            Argument::type(PromotionCouponGeneratorInstructionInterface::class)
        )->shouldBeCalled();

        $this->execute($input, $output);
    }
}
