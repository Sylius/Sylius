<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Factory;

/**
 * Default Resource factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceFactory implements ResourceFactoryInterface
{
    /**
     * Full class name of the resource.
     *
     * @var string
     */
    private $class;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        if (!is_string($class)) {
            throw new \InvalidArgumentException(sprintf('Resource class name must be a string, %s given.', gettype($class)));
        }

        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new $this->class;
    }
}
