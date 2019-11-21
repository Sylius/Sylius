<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Command;

use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCouponsCommand extends Command
{
    /** @var PromotionRepositoryInterface */
    private $promotionRepository;

    /** @var PromotionCouponGeneratorInterface */
    private $couponGenerator;

    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        PromotionCouponGeneratorInterface $couponGenerator
    ) {
        parent::__construct();

        $this->promotionRepository = $promotionRepository;
        $this->couponGenerator = $couponGenerator;
    }

    protected function configure(): void
    {
        $this
            ->setName('sylius:promotion:generate-coupons')
            ->setDescription('Generates coupons for a given promotion')
            ->addArgument('promotion-code', InputArgument::REQUIRED, 'Code of the promotion')
            ->addArgument('count', InputArgument::REQUIRED, 'Amount of coupons to generate')
            ->addOption('length', 'len', InputOption::VALUE_OPTIONAL, 'Length of the coupon code (default 10)', 10)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $promotionCode */
        $promotionCode = $input->getArgument('promotion-code');

        /** @var PromotionInterface|null $promotion */
        $promotion = $this->promotionRepository->findOneBy(['code' => $promotionCode]);

        if ($promotion === null) {
            $output->writeln('<error>No promotion found with this code</error>');

            return 1;
        }

        if (!$promotion->isCouponBased()) {
            $output->writeln('<error>This promotion is not coupon based</error>');

            return 1;
        }

        $instruction = $this->getGeneratorInstructions(
            (int) $input->getArgument('count'),
            (int) $input->getOption('length')
        );

        try {
            $this->couponGenerator->generate($promotion, $instruction);
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');

            return 1;
        }

        $output->writeln('<info>Coupons have been generated</info>');

        return 0;
    }

    public function getGeneratorInstructions(int $count, int $codeLength): PromotionCouponGeneratorInstructionInterface
    {
        $instruction = new PromotionCouponGeneratorInstruction();
        $instruction->setAmount($count);
        $instruction->setCodeLength($codeLength);

        return $instruction;
    }
}
