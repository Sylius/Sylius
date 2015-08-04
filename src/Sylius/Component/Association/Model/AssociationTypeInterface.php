<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Association\Model;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface AssociationTypeInterface
{
    /**
     * @return string
     */
    public function getName();
    /**
     * @param string $name
     */
    public function setName($name);
    /**
     * @return int
     */
    public function getId();
}