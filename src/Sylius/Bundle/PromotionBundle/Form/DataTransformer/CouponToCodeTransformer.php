<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Coupon to code transformer.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponToCodeTransformer implements DataTransformerInterface
{
    /**
     * Coupon repository.
     *
     * @var ObjectRepository
     */
    protected $couponRepository;

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
            throw new UnexpectedTypeException($coupon, CouponInterface::class);
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

        if (!$coupon = $this->couponRepository->findOneBy(['code' => $code])) {
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
