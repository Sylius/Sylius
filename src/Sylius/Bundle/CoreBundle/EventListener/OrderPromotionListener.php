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

use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Order promotion listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param PromotionProcessorInterface $promotionProcessor
     * @param CartProviderInterface       $cartProvider
     * @param SessionInterface            $session
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        PromotionProcessorInterface $promotionProcessor,
        CartProviderInterface $cartProvider,
        SessionInterface $session,
        TranslatorInterface $translator
    )
    {
        $this->promotionProcessor = $promotionProcessor;
        $this->cartProvider = $cartProvider;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * Get the order from event and run the promotion processor on it.
     *
     * @param Event $event
     *
     * @throws UnexpectedTypeException
     */
    public function processOrderPromotion(Event $event)
    {
        $order = $this->cartProvider->getCart();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $this->promotionProcessor->process($order);

        $order->calculateTotal();
    }

    /**
     * Handle coupons added by the user in his cart.
     * TODO: maybe replace this with a unified FlashSubscriber.
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
