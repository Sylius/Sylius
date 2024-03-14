<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\MessageHandler;

use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Message\ResendOrderConfirmationEmail;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResendOrderConfirmationEmailHandler implements MessageHandlerInterface
{
    /**
     * @param RepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderEmailManagerInterface $orderEmailManager,
        private RepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(ResendOrderConfirmationEmail $resendOrderConfirmation): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $resendOrderConfirmation->getOrderTokenValue()]);
        if ($order === null) {
            throw new NotFoundHttpException(sprintf('The order with tokenValue %s has not been found', $resendOrderConfirmation->getOrderTokenValue()));
        }

        $this->orderEmailManager->resendConfirmationEmail($order);
    }
}
