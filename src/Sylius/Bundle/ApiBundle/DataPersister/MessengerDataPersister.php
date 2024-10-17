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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    MessengerDataPersister::class,
);
/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class MessengerDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private ContextAwareDataPersisterInterface $decoratedDataPersister)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decoratedDataPersister->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        try {
            return $this->decoratedDataPersister->persist($data, $context);
        } catch (DelayedMessageHandlingException|HandlerFailedException $e) {
            while ($e instanceof DelayedMessageHandlingException) {
                /** @var \Throwable $e */
                $e = $e->getPrevious();
            }
            while ($e instanceof HandlerFailedException) {
                /** @var \Throwable $e */
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }

    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove($data, $context);
    }
}
