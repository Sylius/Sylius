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

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Default coupon generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGenerator implements CouponGeneratorInterface
{
    /**
     * @var ResourceFactoryInterface
     */
    protected $factory;

    /**
     * @var ResourceRepositoryInterface
     */
    protected $repository;

    /**
     * @var ResourceManagerInterface
     */
    protected $manager;

    /**
     * @param ResourceFactoryInterface $factory
     * @param ResourceRepositoryInterface $repository
     * @param ResourceManagerInterface $manager
     */
    public function __construct(ResourceFactoryInterface $factory, ResourceRepositoryInterface $repository, ResourceManagerInterface $manager)
    {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, Instruction $instruction)
    {
        $generatedCoupons = array();

        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; $i++) {
            $coupon = $this->factory->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setCode($this->generateUniqueCode());
            $coupon->setUsageLimit($instruction->getUsageLimit());
            $coupon->setExpiresAt($instruction->getExpiresAt());

            $generatedCoupons[] = $coupon;

            $this->manager->persist($coupon);
            $this->manager->flush();
        }

        $this->manager->flush();

        return $generatedCoupons;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUniqueCode()
    {
        $code = null;

        do {
            $hash = sha1(microtime(true));
            $code = strtoupper(substr($hash, mt_rand(0, 33), 6));
        } while ($this->isUsedCode($code));

        return $code;
    }

    /**
     * @param string $code
     *
     * @return Boolean
     */
    protected function isUsedCode($code)
    {
        $this->repository->disableFilter('softdeleteable');

        $isUsed = null !== $this->repository->findOneBy(array('code' => $code));

        $this->repository->enableFilter('softdeleteable');

        return $isUsed;
    }
}
