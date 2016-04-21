<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Hydrator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\Hydrator\HydratorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeHydrator implements HydratorInterface
{
    /**
     * @var HydratorInterface
     */
    private $decoratedHydrator;

    /**
     * @param HydratorInterface $decoratedHydrator
     */
    public function __construct(HydratorInterface $decoratedHydrator)
    {
        $this->decoratedHydrator = $decoratedHydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($object)
    {
        $data = $this->decoratedHydrator->extract($object);

        return array_map(function ($value) {
            if ($value instanceof Collection) {
                return $value->toArray();
            }

            return $value;
        }, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object)
    {
        $data = array_map(function ($value) {
            if (is_array($value)) {
                return new ArrayCollection($value);
            }

            return $value;
        }, $data);

        return $this->decoratedHydrator->hydrate($data, $object);
    }
}
