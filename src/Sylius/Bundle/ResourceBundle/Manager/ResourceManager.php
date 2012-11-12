<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Manager;

/**
 * Default resource manager class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class ResourceManager implements ResourceManagerInterface
{
    protected $className;

    public function __construct($className)
    {
        $this->className = $className;
    }

    public function create()
    {
        $class = $this->getClassName();

        return new $class;
    }

    public function getClassName()
    {
        return $this->className;
    }
}
