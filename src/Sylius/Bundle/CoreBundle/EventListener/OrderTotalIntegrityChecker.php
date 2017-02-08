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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderTotalIntegrityChecker
{
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessors;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param OrderProcessorInterface $orderProcessors
     * @param RouterInterface $router
     */
    public function __construct(OrderProcessorInterface $orderProcessors, RouterInterface $router)
    {
        $this->orderProcessors = $orderProcessors;
        $this->router = $router;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function check(ResourceControllerEvent $event)
    {
        /** @var OrderInterface $originalOrder */
        $originalOrder = $event->getSubject();

        if (!$originalOrder instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'This checker can only work with "%s", but got "%s".',
                    OrderInterface::class,
                    get_class($originalOrder)
                )
            );
        }

        $copiedOrder = $originalOrder->getCopy();
        $this->orderProcessors->process($copiedOrder);

        if ($originalOrder->getTotal() !== $copiedOrder->getTotal()) {
            $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR);
            $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));
        }
    }
}
