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

namespace Sylius\Bundle\ApiBundle\Controller;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\Query\GetAddressLogEntry;
use Sylius\Component\Addressing\Model\AddressLogEntry;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/** @experimental */
final class GetAddressLogEntryAction
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /** @return Collection<array-key, AddressLogEntry> */
    public function __invoke(int $id): Collection
    {
        return $this->handle(
            $this->messageBus->dispatch(
                new GetAddressLogEntry($id),
            ),
        );
    }
}
