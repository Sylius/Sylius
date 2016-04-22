<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Promotion\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderPromotionListener
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param GenericEvent $event
     */
    public function handleCouponPromotion(GenericEvent $event)
    {
        if (SyliusPromotionEvents::COUPON_ELIGIBLE === $event->getName()) {
            $type = 'success';
            $message = 'sylius.promotion_coupon.eligible';
        } elseif (SyliusPromotionEvents::COUPON_NOT_ELIGIBLE === $event->getName()) {
            $type = 'error';
            $message = 'sylius.promotion_coupon.not_eligible';
        } else {
            $type = 'error';
            $message = 'sylius.promotion_coupon.invalid';
        }

        $this->session->getBag('flashes')->add($type, $this->translator->trans($message, [], 'flashes'));
    }
}
