<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class UserImpersonatedListener
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SessionInterface $session
     * @param string $sessionKeyName
     * @param ChannelContextInterface $channelContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SessionInterface $session,
        string $sessionKeyName,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->channelContext = $channelContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param UserEvent $event
     */
    public function userImpersonated(UserEvent $event): void
    {
        $customer = $event->getUser()->getCustomer();

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findLatestCartByChannelAndCustomer($channel, $customer);

        $sessionCartKey = sprintf('%s.%s', $this->sessionKeyName, $channel->getCode());

        if ($cart === null) {
            $this->session->remove($sessionCartKey);

            return;
        }

        $this->session->set($sessionCartKey, $cart->getId());
    }
}
