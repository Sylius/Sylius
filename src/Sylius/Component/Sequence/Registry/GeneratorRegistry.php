<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Registry;

use Sylius\Component\Sequence\Number\GeneratorInterface;

/**
 * Registry for generators
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class GeneratorRegistry
{
    /**
     * @var array
     */
    protected $generators = array();

    /**
     * Increment the index and return the new index of the given type
     *
     * @param $interface string
     * @param $generator GeneratorInterface
     */
    public function addGenerator($interface, GeneratorInterface $generator)
    {
        $this->generators[$interface] = $generator;
    }

    /**
     * Return the generator used for the given entity
     *
     * @param $entity
     * @return null
     */
    public function getGenerator($entity)
    {
        foreach ($this->generators as $interface => $generator) {
            if ($entity instanceof $interface) {
                return $generator;
            }
        }

        return null;
    }
}
