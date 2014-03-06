<?php

namespace Sylius\Bundle\FixturesBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;

interface BuilderInterface
{
    /**
     * Get the resource type to build
     *
     * @return string
     */
    public function getResourceClass();

    /**
     * Get the default data set.
     *
     * @return ArrayCollection
     */
    public function getSetDefault();

    /**
     * Get a data set by its name.
     *
     * @param string $name
     * @return ArrayCollection
     * @throws \Exception       in case the set does not exist
     */
    public function getSet($name = 'default');

    /**
     * Get one of the available data set.
     *
     * @return ArrayCollection
     */
    public function getRandomSet();

    /**
     * Get a resource by its name.
     *
     * @param $name
     * @return mixed
     * @throws \Exception   in case the resource does not exist
     */
    public function getResource($name);
} 