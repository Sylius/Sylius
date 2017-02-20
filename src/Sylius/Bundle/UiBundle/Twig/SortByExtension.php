<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\Twig;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class SortByExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sort_by', [$this, 'sortBy']),
        ];
    }

    /**
     * @param array|Collection $array
     * @param string $field
     * @param string $order
     *
     * @return array
     *
     * @throws NoSuchPropertyException
     */
    public function sortBy($array, $field, $order = 'ASC')
    {
        if ($array instanceof Collection) {
            $array = $array->toArray();
        }
        if (1 >= count($array)) {
            return $array;
        }

        /** "@usort" so it won't explode on php 5.6 */
        @usort($array, function ($firstElement, $secondElement) use ($field, $order) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $firstProperty = $accessor->getValue($firstElement, $field);
            $secondProperty = $accessor->getValue($secondElement, $field);

            $result = strcasecmp($firstProperty, $secondProperty);
            if ('DESC' === $order) {
                $result *= -1;
            }

            return $result;
        });

        return $array;
    }
}
