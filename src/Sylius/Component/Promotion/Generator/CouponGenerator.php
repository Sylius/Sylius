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
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * Default coupon generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGenerator implements CouponGeneratorInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, Instruction $instruction)
    {
        $repository = $this->manager->getRepository('Sylius\Component\Promotion\Model\CouponInterface');

        /** @var $coupon CouponInterface */
        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; $i++) {
            $coupon = $repository->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setUsageLimit($instruction->getUsageLimit());

            $this->manager->persist($coupon);
        }

        $this->manager->flush();
    }
}
