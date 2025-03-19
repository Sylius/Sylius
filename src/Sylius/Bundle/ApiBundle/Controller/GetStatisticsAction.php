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
use Sylius\Bundle\ApiBundle\Validator\Constraints\Code;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as SymfonyConstraints;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    /** @var array<array-key, Constraint> */
    private array $constraints;

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
        $this->constraints = $this->createInputDataConstraints();
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request): Response
    {
        $parameters = $request->query->all();

        $violations = $this->validator->validate($parameters, $this->constraints);
        if (count($violations) > 0) {
            return $this->createValidationErrorResponse($violations);
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

    /** @return array<array-key, Constraint> */
    private function createInputDataConstraints(): array
    {
        return [
            new SymfonyConstraints\Collection([
                'channelCode' => new Code(),
                'startDate' => [
                    new SymfonyConstraints\NotBlank(),
                    new SymfonyConstraints\DateTime('Y-m-d\TH:i:s', message: 'sylius.date_time.invalid'),
                ],
                'interval' => new SymfonyConstraints\Choice(choices: array_keys($this->intervalsMap), multiple: false),
                'endDate' => [
                    new SymfonyConstraints\NotBlank(),
                    new SymfonyConstraints\DateTime('Y-m-d\TH:i:s', message: 'sylius.date_time.invalid'),
                ],
            ]),
            new SymfonyConstraints\Callback(function (array $data, ExecutionContextInterface $context) {
                $this->validateDateRange($data, $context);
            }),
        ];
    }

    /** @param array<array-key, mixed> $data */
    private function validateDateRange(array $data, ExecutionContextInterface $context): void
    {
        if (isset($data['startDate'], $data['endDate']) && $data['startDate'] >= $data['endDate']) {
            $context->buildViolation('sylius.statistics.end_date.invalid')->addViolation();
        }
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

    private function createValidationErrorResponse(ConstraintViolationListInterface $violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return new JsonResponse([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'violations' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
