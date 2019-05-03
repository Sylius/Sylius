<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Command;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PromotionRepository;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCouponsCommand extends Command
{
    /** @var PromotionRepository */
    private $promotionRepository;

    /** @var PromotionCouponGeneratorInterface */
    private $couponGenerator;

    /**
     * GenerateCouponsCommand constructor.
     *
     * @param PromotionRepository               $promotionRepository
     * @param PromotionCouponGeneratorInterface $couponGenerator
     */
    public function __construct(
        PromotionRepository $promotionRepository,
        PromotionCouponGeneratorInterface $couponGenerator
    ) {
        parent::__construct(null);
        $this->promotionRepository = $promotionRepository;
        $this->couponGenerator     = $couponGenerator;
    }

    /**  {@inheritdoc} */
    protected function configure(): void
    {
        $this
            ->setName('sylius:promotion:generate-coupons')
            ->setDescription('Generates coupons for a given promotion')
            ->addArgument('promotionCode', InputArgument::REQUIRED, 'Code of the promotion')
            ->addArgument('count', InputArgument::REQUIRED, 'Amount of coupons to generate')
            ->addOption('length', 'len', InputOption::VALUE_OPTIONAL, 'Length of the coupon code (default 10)', '10')
        ;
    }

    /**  {@inheritdoc} */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var string $promotionCode */
        $promotionCode = $input->getArgument('promotionCode');

        /** @var PromotionInterface|null $promotion */
        $promotion = $this->promotionRepository->findOneBy(['code' => $promotionCode]);
        if ($promotion === null) {
            $output->writeln('<error>No promotion found with this code</error>');

            return;
        }

        if (!$promotion->isCouponBased()) {
            $output->writeln('<error>This promotion is not Coupon based</error>');

            return;
        }

        $instruction = $this->getGeneratorInstructions(
            (int) $input->getArgument('count'),
            (int) $input->getArgument('length')
        );
        // Generates the promotions
        $this->couponGenerator->generate($promotion, $instruction);
    }

    /**
     * Creates a instruction object for the coupon generator
     *
     * @param int $count   How many coupons should be generated
     * @param int $codeLength
     *
     * @return PromotionCouponGeneratorInstructionInterface
     */
    public function getGeneratorInstructions(int $count, int $codeLength): PromotionCouponGeneratorInstructionInterface
    {
        $instruction = new PromotionCouponGeneratorInstruction();
        $instruction->setAmount($count);
        $instruction->setCodeLength($codeLength);

        return $instruction;
    }
}
