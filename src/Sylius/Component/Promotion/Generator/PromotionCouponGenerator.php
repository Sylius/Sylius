<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Generator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Promotion\Exception\FailedGenerationException;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PromotionCouponGenerator implements PromotionCouponGeneratorInterface
{
    /**
     * @var FactoryInterface
     */
    private $couponFactory;

    /**
     * @var PromotionCouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var GenerationPolicyInterface
     */
    private $generationPolicy;

    /**
     * @param FactoryInterface $couponFactory
     * @param PromotionCouponRepositoryInterface $couponRepository
     * @param ObjectManager $objectManager
     * @param GenerationPolicyInterface $generationPolicy
     */
    public function __construct(
        FactoryInterface $couponFactory,
        PromotionCouponRepositoryInterface $couponRepository,
        ObjectManager $objectManager,
        GenerationPolicyInterface $generationPolicy
    ) {
        $this->couponFactory = $couponFactory;
        $this->couponRepository = $couponRepository;
        $this->objectManager = $objectManager;
        $this->generationPolicy = $generationPolicy;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, PromotionCouponGeneratorInstructionInterface $instruction)
    {
        $generatedCoupons = [];

        $this->assertGenerationIsPossible($instruction);
        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; ++$i) {
            $code = $this->generateUniqueCode($instruction->getCodeLength(), $generatedCoupons);
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
     * @param int $codeLength
     * @param array $generatedCoupons
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function generateUniqueCode($codeLength, array $generatedCoupons)
    {
        Assert::nullOrRange($codeLength, 1, 40, 'Invalid %d code length should be between %d and %d');

        do {
            $hash = sha1(microtime(true));
            $code = strtoupper(substr($hash, 0, $codeLength));
        } while ($this->isUsedCode($code, $generatedCoupons));

        return $code;
    }

    /**
     * @param string $code
     * @param array $generatedCoupons
     *
     * @return bool
     */
    private function isUsedCode($code, array $generatedCoupons)
    {
        if (isset($generatedCoupons[$code])) {
            return true;
        }

        return null !== $this->couponRepository->findOneBy(['code' => $code]);
    }

    /**
     * @param PromotionCouponGeneratorInstructionInterface $instruction
     *
     * @throws FailedGenerationException
     */
    private function assertGenerationIsPossible(PromotionCouponGeneratorInstructionInterface $instruction)
    {
        if (!$this->generationPolicy->isGenerationPossible($instruction)) {
            throw new FailedGenerationException($instruction);
        }
    }
}
