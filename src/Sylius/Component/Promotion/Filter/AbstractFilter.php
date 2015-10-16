<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException if the filter has not returned Collection or it's not Selectable
     */
    public function apply(Collection $collection, array $configuration = array())
    {
        $this->configuration = $this->resolveConfiguration($configuration);

        $filteredCollection = $this->filter($collection);

        // no possible way of knowing if it would be PersistentCollection or ArrayCollection
        if ($filteredCollection instanceof Collection &&
            $filteredCollection instanceof Selectable
        ) {
            return $filteredCollection;
        }

        throw new \UnexpectedValueException(
            'Filter has not returned an instance of Selectable Collection'
        );
    }

    /**
     * @param Collection $collection
     *
     * @return Collection&Selectable
     */
    protected abstract function filter(Collection $collection);

    protected abstract function resolveConfiguration(array $configuration);
}