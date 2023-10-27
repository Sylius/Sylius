<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ContextAwareFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * When given a date-only "before" value to a DateFilter, allow it to include dates on that day
 *
 * To include a '1970-01-01 13:00', it transforms:
 *      'before' => '1970-01-01'
 * into:
 *      'before' => '1970-01-01 23:59'
 */
final class InclusiveDateRangeFilter implements ContextAwareFilterInterface
{
    /**
     * @param string[] $properties
     */
    public function __construct(private ContextAwareFilterInterface $dateFilter, private array $properties)
    {
    }

    /**
     * @param class-string $resourceClass
     * @return array<string, mixed>
     */
    public function getDescription(string $resourceClass): array
    {
        return $this->dateFilter->getDescription($resourceClass);
    }

    /**
     * @param class-string $resourceClass
     * @param array<string, mixed> $context
     *
     * @throws \Exception
     */
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = []): void
    {
        foreach ($this->properties as $property) {
            $dateBefore = $context['filters'][$property]['before'] ?? null;
            if ($this->isExclusive($dateBefore)) {
                $context['filters'][$property]['before'] = $this->makeDateInclusive($dateBefore);
            }
        }

        $this->dateFilter->apply($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
    }

    protected function makeDateInclusive(string $value): string
    {
        return (new \DateTimeImmutable($value))
            ->setTime(23, 59)
            ->format('Y-m-d H:i');
    }

    private function isExclusive(mixed $value): bool
    {
        if (!$value || !is_string($value)) {
            return false;
        }

        $dateParts = date_parse($value);

        if (!empty($dateParts['errors'])) {
            return false;
        }

        return empty($dateParts['hour']) && empty($dateParts['minute']);
    }
}
