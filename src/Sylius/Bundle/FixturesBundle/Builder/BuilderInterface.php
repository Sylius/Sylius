<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface for data set builders.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
interface BuilderInterface
{
    /**
     * Get the default data set.
     *
     * @return ArrayCollection
     */
    public function getDataSetDefault();

    /**
     * Get a data set by its name.
     *
     * @param string $name
     * @return ArrayCollection
     * @throws \Exception       in case the set does not exist
     */
    public function getDataSet($name = 'default');

    /**
     * Get one of the available data set.
     *
     * @return ArrayCollection
     */
    public function getRandomDataSet();

    /**
     * Get a resource by its name.
     *
     * @param $name
     * @return mixed
     * @throws \Exception   in case the resource does not exist
     */
    public function getResource($name);
} 