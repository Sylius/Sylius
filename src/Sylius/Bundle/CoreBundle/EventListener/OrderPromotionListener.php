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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
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
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Boolean
     */
    private $itemBased;

    /**
     * Constructor.
     *
     * @param PromotionProcessorInterface $promotionProcessor
     * @param SessionInterface            $session
     * @param TranslatorInterface         $translator
     * @param Boolean                     $itemBased
     */
    public function __construct(
        PromotionProcessorInterface $promotionProcessor,
        SessionInterface $session,
        TranslatorInterface $translator,
        $itemBased
    )
    {
        $this->promotionProcessor = $promotionProcessor;
        $this->session = $session;
        $this->translator = $translator;
        $this->itemBased = $itemBased;
    }

    /**
     * Get the order from event and run the promotion processor on it.
     *
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function processOrderPromotion(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $this->promotionProcessor->process($order);

        if ($this->itemBased) {
            foreach ($order->getItems() as $item) {
                $this->promotionProcessor->process($item);
            }
        }

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
