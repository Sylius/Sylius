<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Generator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Default coupon generator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CouponGenerator implements CouponGeneratorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var ObjectManager
     */
    protected $manager;

    public function __construct(RepositoryInterface $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, Instruction $instruction)
    {
        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; $i++) {
            $coupon = $this->repository->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setCode($this->generateUniqueCode());
            $coupon->setUsageLimit($instruction->getUsageLimit());

            $this->manager->persist($coupon);
        }

        $this->manager->flush();
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
     * @return boolean
     */
    protected function isUsedCode($code)
    {
        return null !== $this->repository->findOneBy(array('code' => $code));
    }
}
