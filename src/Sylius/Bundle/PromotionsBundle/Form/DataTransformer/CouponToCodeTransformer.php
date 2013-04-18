<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\PromotionsBundle\Model\CouponInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Coupon to code transformer.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CouponToCodeTransformer implements DataTransformerInterface
{
    /**
     * Coupon repository.
     *
     * @var ObjectRepository
     */
    private $couponRepository;

    /**
     * Constructor.
     *
     * @param ObjectRepository $couponRepository
     */
    public function __construct(ObjectRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($coupon)
    {
        if (null === $coupon) {
            return '';
        }

        if (!$coupon instanceof CouponInterface) {
            throw new UnexpectedTypeException($coupon, 'Sylius\Bundle\PromotionsBundle\Model\CouponInterface');
        }

        return $coupon->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($code)
    {
        if (!$code) {
            return null;
        }

        if (!$coupon = $this->couponRepository->findOneBy(array('code' => $code))) {
            return null;
        }

        return $coupon->isValid() ? $coupon : null;
    }
}
