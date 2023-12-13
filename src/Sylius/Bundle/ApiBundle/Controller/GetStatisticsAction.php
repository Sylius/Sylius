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

use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Sylius\Component\Core\DateTime\Period;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus, private SerializerInterface $serializer)
    {
        $this->messageBus = $queryBus;
    }

    public function __invoke(Request $request): Response
    {
        $channelCode = $request->query->get('channelCode');

        if ($channelCode === null) {
            return new JsonResponse(['error' => 'Missing channelCode parameter.'], Response::HTTP_BAD_REQUEST);
        }

        $period = new Period(
            new \DateTimeImmutable('first day of january this year 00:00:00'),
            new \DateTimeImmutable('last day of december this year 23:59:59'),
            \DateInterval::createFromDateString('1 month'),
        );

        return new JsonResponse(
            data: $this->serializer->serialize($this->handle(new GetStatistics($period, $channelCode)), 'json'),
            json: true,
        );
    }
}
