<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Twig;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SortByExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('sort_by', [$this, 'sortBy']),
        ];
    }

    /**
     * @param iterable $iterable
     * @param string $field
     * @param string $order
     *
     * @return array
     *
     * @throws NoSuchPropertyException
     */
    public function sortBy(iterable $iterable, string $field, string $order = 'ASC'): array
    {
        $array = $this->transformIterableToArray($iterable);

        usort($array, function ($firstElement, $secondElement) use ($field, $order) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $firstProperty = (string) $accessor->getValue($firstElement, $field);
            $secondProperty = (string) $accessor->getValue($secondElement, $field);

            $result = strcasecmp($firstProperty, $secondProperty);
            if ('DESC' === $order) {
                $result *= -1;
            }

            return $result;
        });

        return $array;
    }

    /**
     * @param iterable $iterable
     *
     * @return array
     */
    private function transformIterableToArray(iterable $iterable): array
    {
        if (is_array($iterable)) {
            return $iterable;
        }

        if ($iterable instanceof \Traversable) {
            return iterator_to_array($iterable);
        }

        throw new \RuntimeException('Cannot transform an iterable to an array.');
    }
}
