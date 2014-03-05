<?php

namespace Sylius\Bundle\FixturesBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;

interface BuilderInterface
{
    public function getModelClass();

    public function getElement($name);

    /**
     * @param string $name
     * @return ArrayCollection
     */
    public function getSet($name = 'default');

    /**
     * @return ArrayCollection
     */
    public function getRandomSet();
} 