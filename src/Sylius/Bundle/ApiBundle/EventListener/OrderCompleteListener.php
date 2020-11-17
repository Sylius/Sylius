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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Sylius\Bundle\ApiBundle\Command\SendOrderConfirmation;
use Sylius\Bundle\ApiBundle\Event\OrderCompletedEvent;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderCompleteListener
{
    /** @var MessageBusInterface */
    private $bus;

    /** @var OrderRepository */
    private $orderRepository;

    public function __construct(MessageBusInterface $bus, OrderRepository $orderRepository)
    {
        $this->bus = $bus;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(OrderCompletedEvent $orderCompleted): void
    {
        $order = $this->orderRepository->findOneByTokenValue($orderCompleted->orderToken());

        $this->bus->dispatch(new Envelope(new SendOrderConfirmation($order)));
    }
}
