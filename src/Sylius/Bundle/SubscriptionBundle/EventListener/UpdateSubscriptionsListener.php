<?php

namespace Sylius\Bundle\SubscriptionBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UpdateSubscriptionsListener
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(ObjectManager $manager, SecurityContextInterface $securityContext)
    {
        $this->manager = $manager;
        $this->securityContext = $securityContext;
    }

    /**
     * Save & persist the subscriptions.
     *
     * @param CartEvent $event
     */
    public function onCartSave(CartEvent $event)
    {
        $cart = $event->getCart();

        foreach ($cart->getItems() as $item) { /** @var OrderItemInterface $item */
            if (null === $subscription = $item->getSubscription()) {
                continue;
            }

            $subscription->setVariant($item->getVariant());
            $subscription->setQuantity($item->getQuantity());
            $subscription->setScheduledDate(new \DateTime());

            if (null !== $token = $this->securityContext->getToken()) {
                $subscription->setUser($token->getUser());
            }

            if (null === $subscription->getId()) {
                $this->manager->persist($subscription);
            }
        }

        $this->manager->flush();
    }
}
