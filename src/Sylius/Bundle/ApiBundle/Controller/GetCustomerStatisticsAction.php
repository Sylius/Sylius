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

use Sylius\Bundle\ApiBundle\Query\GetCustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatistics;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class GetCustomerStatisticsAction
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(int $id): CustomerStatistics
    {
        return $this->handle(
            $this->messageBus->dispatch(
                new GetCustomerStatistics($id),
            ),
        );
    }
}
