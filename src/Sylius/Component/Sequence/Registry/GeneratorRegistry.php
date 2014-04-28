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

use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Sequence\Number\GeneratorInterface;

/**
 * Registry for generators
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class GeneratorRegistry extends ServiceRegistry
{
    /**
     * Return the generator used for the given entity
     *
     * @param object $entity
     *
     * @return GeneratorInterface
     *
     * @throws NonExistingServiceException
     */
    public function get($entity)
    {
        foreach ($this->services as $interface => $generator) {
            if ($entity instanceof $interface) {
                return $generator;
            }
        }

        throw new NonExistingServiceException(get_class($entity));
    }
}
