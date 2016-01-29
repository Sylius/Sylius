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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGenerator implements CouponGeneratorInterface
{
    /**
     * @var FactoryInterface
     */
    protected $couponFactory;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @param FactoryInterface $couponFactory
     * @param RepositoryInterface $repository
     * @param EntityManagerInterface $manager
     */
    public function __construct(FactoryInterface $couponFactory, RepositoryInterface $repository, EntityManagerInterface $manager)
    {
        $this->couponFactory = $couponFactory;
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, Instruction $instruction)
    {
        $generatedCoupons = [];
        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; ++$i) {
            $coupon = $this->couponFactory->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setCode($this->generateUniqueCode());
            $coupon->setUsageLimit($instruction->getUsageLimit());
            $coupon->setExpiresAt($instruction->getExpiresAt());

            $generatedCoupons[] = $coupon;

            $this->manager->persist($coupon);
        }

        $this->manager->flush();

        return $generatedCoupons;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUniqueCode()
    {
        $this->manager->getFilters()->disable('softdeleteable');

        do {
            $hash = sha1(microtime(true));
            $code = strtoupper(substr($hash, mt_rand(0, 33), 6));
        } while ($this->isUsedCode($code));

        $this->manager->getFilters()->enable('softdeleteable');

        return $code;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    protected function isUsedCode($code)
    {
        return null !== $this->repository->findOneBy(['code' => $code]);
    }
}
