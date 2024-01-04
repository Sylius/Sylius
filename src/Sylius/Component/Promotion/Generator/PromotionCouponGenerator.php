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

namespace Sylius\Component\Promotion\Generator;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Promotion\Exception\FailedGenerationException;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class PromotionCouponGenerator implements PromotionCouponGeneratorInterface
{
    public function __construct(
        private FactoryInterface $couponFactory,
        private PromotionCouponRepositoryInterface $couponRepository,
        private ObjectManager $objectManager,
        private GenerationPolicyInterface $generationPolicy,
    ) {
    }

    public function generate(
        PromotionInterface $promotion,
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
    ): array {
        $generatedCoupons = [];

        $this->assertGenerationIsPossible($instruction);
        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; ++$i) {
            $code = $this->generateUniqueCode(
                $instruction->getCodeLength(),
                $generatedCoupons,
                $instruction->getPrefix(),
                $instruction->getSuffix(),
            );

            /** @var PromotionCouponInterface $coupon */
            $coupon = $this->couponFactory->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setCode($code);
            $coupon->setUsageLimit($instruction->getUsageLimit());
            $coupon->setExpiresAt($instruction->getExpiresAt());

            $generatedCoupons[$code] = $coupon;

            $this->objectManager->persist($coupon);
        }

        $this->objectManager->flush();

        return $generatedCoupons;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function generateUniqueCode(
        int $codeLength,
        array $generatedCoupons,
        ?string $prefix,
        ?string $suffix,
    ): string {
        Assert::nullOrRange($codeLength, 1, 40, 'Invalid %d code length should be between %d and %d');

        do {
            $hash = bin2hex(random_bytes(20));
            $code = $prefix . strtoupper(substr($hash, 0, $codeLength)) . $suffix;
        } while ($this->isUsedCode($code, $generatedCoupons));

        return $code;
    }

    private function isUsedCode(string $code, array $generatedCoupons): bool
    {
        if (isset($generatedCoupons[$code])) {
            return true;
        }

        return null !== $this->couponRepository->findOneBy(['code' => $code]);
    }

    /**
     * @throws FailedGenerationException
     */
    private function assertGenerationIsPossible(ReadablePromotionCouponGeneratorInstructionInterface $instruction): void
    {
        if (!$this->generationPolicy->isGenerationPossible($instruction)) {
            throw new FailedGenerationException($instruction);
        }
    }
}
