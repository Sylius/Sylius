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
use Sylius\Bundle\ApiBundle\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as SymfonyAssert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    private SymfonyAssert\Collection $constraint;

    /** @var array<string, string> */
    private array $intervalsMap;

    /** @param array<string, array{interval: string, period_format: string}> $intervalsMap */
    public function __construct(
        MessageBusInterface $messageBus,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        array $intervalsMap,
    ) {
        $this->messageBus = $messageBus;
        $this->intervalsMap = $this->populateIntervals($intervalsMap);
        $this->constraint = $this->createInputDataConstraints();
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request): Response
    {
        $parameters = $request->query->all();

        $violations = $this->validator->validate($parameters, $this->constraint);
        if (count($violations) > 0) {
            return $this->createBadRequestResponse($violations);
        }

        $interval = $parameters['interval'];

        $period = new \DatePeriod(
            new \DateTimeImmutable($parameters['startDate']),
            new \DateInterval($this->intervalsMap[$interval]),
            new \DateTimeImmutable($parameters['endDate']),
        );

        try {
            $result = $this->handle(new GetStatistics(
                $interval,
                $period,
                $parameters['channelCode'],
            ));

            return new JsonResponse(
                data: $this->serializer->serialize($result, 'json'),
                status: Response::HTTP_OK,
                json: true,
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function createBadRequestResponse(ConstraintViolationListInterface $violations): JsonResponse
    {
        return new JsonResponse(
            data: $this->serializer->serialize($violations, 'json'),
            status: Response::HTTP_BAD_REQUEST,
            json: true,
        );
    }

    private function createInputDataConstraints(): SymfonyAssert\Collection
    {
        return new SymfonyAssert\Collection([
            'channelCode' => new Assert\Code(),
            'startDate' => [
                new SymfonyAssert\NotBlank(),
                new SymfonyAssert\DateTime('Y-m-d\TH:i:s', message: 'sylius.date_time.invalid'),
            ],
            'interval' => new SymfonyAssert\Choice(choices: array_keys($this->intervalsMap), multiple: false),
            'endDate' => [
                new SymfonyAssert\NotBlank(),
                new SymfonyAssert\DateTime('Y-m-d\TH:i:s', message: 'sylius.date_time.invalid'),
                new SymfonyAssert\GreaterThan(propertyPath: 'startDate'),
            ],
        ]);
    }

    /**
     * @param array<string, array{interval: string, period_format: string}> $intervalsMap
     *
     * @return array<string, string>
     */
    private function populateIntervals(array $intervalsMap): array
    {
        $intervals = [];
        foreach ($intervalsMap as $type => $intervalMap) {
            $intervals[$type] = $intervalMap['interval'];
        }

        return $intervals;
    }
}
