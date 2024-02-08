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

namespace Sylius\Bundle\UiBundle\Twig;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortByExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('sort_by', [$this, 'sortBy']),
        ];
    }

    /**
     * @throws NoSuchPropertyException
     */
    public function sortBy(iterable $iterable, string $field, string $order = 'ASC'): array
    {
        $array = $this->transformIterableToArray($iterable);

        usort(
            $array,
            /**
             * @param mixed $firstElement
             * @param mixed $secondElement
             */
            function ($firstElement, $secondElement) use ($field, $order) {
                $accessor = PropertyAccess::createPropertyAccessor();

                $firstProperty = (string) $accessor->getValue($firstElement, $field);
                $secondProperty = (string) $accessor->getValue($secondElement, $field);

                $result = strnatcasecmp($firstProperty, $secondProperty);
                if ('DESC' === $order) {
                    $result *= -1;
                }

                return $result;
            },
        );

        return $array;
    }

    private function transformIterableToArray(iterable $iterable): array
    {
        if (is_array($iterable)) {
            return $iterable;
        }

        return iterator_to_array($iterable);
    }
}
