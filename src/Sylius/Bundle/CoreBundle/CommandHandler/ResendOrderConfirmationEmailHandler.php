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

namespace Sylius\Bundle\CoreBundle\CommandHandler;

use Sylius\Bundle\CoreBundle\Command\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ResendOrderConfirmationEmailHandler
{
    /** @param RepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private OrderEmailManagerInterface $orderEmailManager,
        private RepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(ResendOrderConfirmationEmail $resendOrderConfirmation): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $resendOrderConfirmation->orderTokenValue]);
        if ($order === null) {
            throw new NotFoundHttpException(sprintf('The order with tokenValue %s has not been found', $resendOrderConfirmation->orderTokenValue));
        }

        $this->orderEmailManager->resendConfirmationEmail($order);
    }
}
