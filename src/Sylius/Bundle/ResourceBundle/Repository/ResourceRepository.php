<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Repository;

/**
 * Base Doctrine resource manager class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class ResourceRepository implements ResourceRepositoryInterface
{
    protected $className;

    public function __construct($className)
    {
        $this->className = $className;
    }

    public function getClassName()
    {
        return $this->className;
    }
}
