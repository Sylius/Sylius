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

namespace Sylius\Bundle\PaymentBundle\EventListener;

use Doctrine\ORM\Event\PostUpdateEventArgs;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

/** @experimental */
final class PaymentMethodChangeEventListener
{
    public function __construct(private PaymentRequestCancellerInterface $paymentRequestCanceller)
    {
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof PaymentInterface) {
            return;
        }

        /** @var array<string, array<int, mixed>> $changeSet */
        $changeSet = $args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity);

        if (array_key_exists('method', $changeSet)) {
            [$oldMethod, $newMethod] = $changeSet['method'];

            if ($oldMethod !== $newMethod) {
                $this->paymentRequestCanceller->cancelPaymentRequests($entity->getId(), $newMethod->getCode());
            }
        }
    }
}
