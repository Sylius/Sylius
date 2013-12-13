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

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Processor\PromotionProcessorInterface;
use Sylius\Bundle\PromotionsBundle\SyliusPromotionEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Order promotion listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderPromotionListener
{
    /**
     * Order promotion processor.
     *
     * @var PromotionProcessorInterface
     */
    protected $promotionProcessor;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param PromotionProcessorInterface $promotionProcessor
     * @param SessionInterface            $session
     * @param TranslatorInterface         $translator
     */
    public function __construct(PromotionProcessorInterface $promotionProcessor, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->promotionProcessor = $promotionProcessor;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * Get the order from event and run the promotion processor on it.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function processOrderPromotion(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order promotion listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->promotionProcessor->process($order);

        $order->calculateTotal();
    }

    /**
     * Handle coupons added by the user in his cart.
     * TODO: maybe replace this with a unified FlashListener.
     *
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

        $this->session->getBag('flashes')->add($type, $this->translator->trans($message, array(), 'flashes'));
    }
}
