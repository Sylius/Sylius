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
use Sylius\Bundle\PromotionsBundle\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param ObjectRepository         $couponRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ObjectRepository $couponRepository, EventDispatcherInterface $dispatcher)
    {
        $this->couponRepository = $couponRepository;
        $this->dispatcher = $dispatcher;
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
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_INVALID, new GenericEvent());

            return null;
        }

        if (!$coupon->isValid()) {
            $this->dispatcher->dispatch(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE, new GenericEvent());

            return null;
        }

        return $coupon;
    }
}
