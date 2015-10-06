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

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException if the filter has not returned ArrayCollection
     */
    public function apply(ArrayCollection $collection, array $configuration = array())
    {
        $this->configuration = $this->resolveConfiguration($configuration);

        $filteredCollection = $this->filter($collection);

        if ($filteredCollection instanceof ArrayCollection) {
            return $filteredCollection;
        }

        throw new \UnexpectedValueException(
            'Filter has not returned an instance of ArrayCollection'
        );
    }

    /**
     * @param ArrayCollection $collection
     *
     * @return ArrayCollection
     */
    protected abstract function filter(ArrayCollection $collection);

    protected abstract function resolveConfiguration(array $configuration);
}