<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\FixturesBundle\Builder\BuilderInterface;

/**
 * Interface for data set loaders.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads a data set.
     *
     * @param string $type      type of data set to load
     * @param string $name      name of the data set (if null, a random data set is loaded)
     *
     * @return ArrayCollection
     */
    public function loadSet($type, $name = 'default');

    /**
     * Returns a data set builder.
     *
     * @param $type
     *
     * @return BuilderInterface
     * @throws \Exception       in case the type is not handled
     */
    public function getBuilder($type);

    /**
     * Adds a data set builder.
     *
     * @param BuilderInterface $builder
     * @return LoaderInterface
     */
    public function setBuilder(BuilderInterface $builder);
} 