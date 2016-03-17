<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\MultiContainerExtension;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ContextRegistry
{
    /**
     * @var array
     */
    private $registry;

    /**
     * @param string $serviceId
     * @param string $serviceClass
     */
    public function add($serviceId, $serviceClass)
    {
        $this->registry[$serviceId] = $serviceClass;
    }

    /**
     * @param string $serviceId
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getClass($serviceId)
    {
        if (!isset($this->registry[$serviceId])) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find class for service with id "%s". Make sure you tagged the context with "sylius.behat.context".',
                $serviceId
            ));
        }

        return $this->registry[$serviceId];
    }
}
