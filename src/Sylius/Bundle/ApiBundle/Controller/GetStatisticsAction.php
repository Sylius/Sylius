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

use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Sylius\Bundle\ApiBundle\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as BaseAssert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
        $this->messageBus = $queryBus;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request): Response
    {
        $constraint = new BaseAssert\Collection([
            'channelCode' => new Assert\Code(),
            'startDate' => new BaseAssert\DateTime('Y-m-d\TH:i:s', message: 'sylius.date_time.invalid'),
            'dateInterval' => new Assert\DateInterval(),
            'endDate' => new BaseAssert\DateTime('Y-m-d\TH:i:s', message: 'sylius.date_time.invalid'),
        ]);

        $violations = $this->validator->validate($request->query->all(), $constraint);

        if (count($violations) > 0) {
            return new JsonResponse(
                data: $this->serializer->serialize($violations, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            );
        }

        $parameters = $request->query->all();
        $period = new \DatePeriod(
            new \DateTimeImmutable($parameters['startDate']),
            new \DateInterval($parameters['dateInterval']),
            new \DateTimeImmutable($parameters['endDate']),
        );

        $violations = $this->validator->validate($period, new Assert\DatePeriod());

        if (count($violations) > 0) {
            return new JsonResponse(
                data: $this->serializer->serialize($violations, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            );
        }

        try {
            $result = $this->handle(new GetStatistics($period, $parameters['channelCode']));
            $status = Response::HTTP_OK;
        } catch (HandlerFailedException $exception) {
            $exception = $exception->getPrevious();
            $result = ['message' => $exception->getMessage()];

            if ($exception instanceof ChannelNotFoundException) {
                $status = Response::HTTP_NOT_FOUND;
            } else {
                throw $exception;
            }
        }

        return new JsonResponse(data: $this->serializer->serialize($result, 'json'), status: $status, json: true);
    }
}
