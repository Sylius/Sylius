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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    /** @var array<array{queryParameter: string, message: string}> */
    private array $violations = [];

    /** @var array<string> */
    private array $requiredParameters = [
        'channelCode' => 'string',
        'startDate' => 'dateTime',
        'dateInterval' => 'dateInterval',
        'endDate' => 'dateTime',
    ];

    public function __construct(
        MessageBusInterface $queryBus,
        private SerializerInterface $serializer,
    ) {
        $this->messageBus = $queryBus;
    }

    public function __invoke(Request $request): Response
    {
        $this->validateRequiredParameters($request);

        $parameters = $request->query->all();

        if (count($this->violations) > 0) {
            return new JsonResponse(
                data: $this->serializer->serialize($this->violations, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            );
        }

        $period = new \DatePeriod(
            new \DateTimeImmutable($parameters['startDate']),
            new \DateInterval($parameters['dateInterval']),
            new \DateTimeImmutable($parameters['endDate']),
        );

        try {
            $this->validateEndDateIsNotBeforeStartDate($period);
            $this->validateEndDateAgainstInterval($period);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(
                data: $this->serializer->serialize(['message' => $exception->getMessage()], 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            );
        }

        $query = new GetStatistics($period, $parameters['channelCode']);

        try {
            $result = $this->handle($query);
        } catch (HandlerFailedException $exception) {
            return new JsonResponse(
                data: $this->serializer->serialize(['message' => $exception->getMessage()], 'json'),
                status: Response::HTTP_NOT_FOUND,
                json: true,
            );
        }

        return new JsonResponse(data: $this->serializer->serialize($result, 'json'), json: true);
    }

    private function validateRequiredParameters(Request $request): void
    {
        foreach ($this->requiredParameters as $parameterName => $parameterType) {
            $parameter = $request->query->get($parameterName);

            if ($parameter === null) {
                $this->addViolation($parameterName, sprintf('Parameter "%s" is required.', $parameterName));

                continue;
            }

            if (empty($parameter)) {
                $this->addViolation($parameterName, sprintf('Parameter "%s" cannot be empty.', $parameterName));

                continue;
            }

            if ($parameterType === 'dateTime' && !$this->isISO8601DateTimeWithNoTimezone($parameter)) {
                $this->addViolation($parameterName, sprintf(
                    'Parameter "%s" must be a valid ISO8601 date time string without timezone.',
                    $parameterName,
                ));
            } elseif ($parameterType === 'dateInterval' && !$this->isValidInterval($parameter)) {
                $this->addViolation($parameterName, sprintf(
                    'Parameter "%s" must be a valid DateInterval string.',
                    $parameterName,
                ));
            } elseif ($parameterType === 'int' && filter_var($parameter, \FILTER_VALIDATE_INT) === false) {
                $this->addViolation($parameterName, sprintf(
                    'Parameter "%s" must be an integer.',
                    $parameterName,
                ));
            } elseif (!is_string($parameter) || is_numeric($parameter)) {
                $this->addViolation($parameterName, sprintf(
                    'Parameter "%s" must be a string.',
                    $parameterName,
                ));
            }
        }
    }

    private function isISO8601DateTimeWithNoTimezone(?string $dateTime = null): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $dateTime) === 1;
    }

    private function isValidInterval(?string $interval = null): bool
    {
        try {
            new \DateInterval($interval);
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    /**
     * Validates that the end date is a multiple of the interval.
     * The end date is adjusted by subtracting one second to make it inclusive (closed interval).
     * If the adjusted end date does not match the provided end date, an exception is thrown.
     */
    private function validateEndDateAgainstInterval(\DatePeriod $datePeriod): void
    {
        $currentDate = clone $datePeriod->getStartDate();
        $endDate = $datePeriod->getEndDate();
        $interval = $datePeriod->getDateInterval();

        while ($currentDate <= $endDate) {
            $currentDate = $currentDate->add($interval);
        }

        /** We shift to make closed interval. */
        $intervalEndDate = $currentDate->modify('-1 second');

        if ($intervalEndDate != $endDate) {
            throw new \InvalidArgumentException(sprintf(
                sprintf(
                    'End date "%s" must be multiple of interval, expected "%s"',
                    $endDate->format('Y-m-d H:i:s'),
                    $intervalEndDate->format('Y-m-d H:i:s'),
                ),
            ));
        }
    }

    private function validateEndDateIsNotBeforeStartDate(\DatePeriod $period): void
    {
        if ($period->getStartDate() > $period->getEndDate()) {
            throw new \InvalidArgumentException(sprintf(
                'End date "%s" must be after start date "%s"',
                $period->getEndDate()->format('Y-m-d H:i:s'),
                $period->getStartDate()->format('Y-m-d H:i:s'),
            ));
        }
    }

    private function addViolation(string $parameterName, string $message): void
    {
        $this->violations[] = ['queryParameter' => $parameterName, 'message' => $message];
    }
}
