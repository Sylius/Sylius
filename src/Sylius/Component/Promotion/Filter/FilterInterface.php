<?php

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Created by PhpStorm.
 * User: piotrwalkow
 * Date: 28/09/15
 * Time: 17:58
 */
interface FilterInterface
{
    /**
     * @param ArrayCollection $arrayCollection
     *
     * @return ArrayCollection
     */
    public function apply(ArrayCollection $arrayCollection);
}