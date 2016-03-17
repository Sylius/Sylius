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

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ContainerConfiguration
{
    /**
     * @var string
     */
    private $defaultContainerName;

    /**
     * @var array
     */
    private $containers = [];

    /**
     * @param string $defaultContainerName
     */
    public function __construct($defaultContainerName)
    {
        $this->defaultContainerName = $defaultContainerName;
    }

    /**
     * @param string $containerName
     * @param string $containerId
     */
    public function addContainer($containerName, $containerId)
    {
        $this->containers[$containerName] = $containerId;
    }

    /**
     * @param string|null $containerName
     *
     * @return bool
     */
    public function isDefault($containerName = null)
    {
        return null === $containerName || $containerName === $this->defaultContainerName;
    }

    /**
     * @param string $containerName
     *
     * @return bool
     */
    public function isDefined($containerName)
    {
        return $this->isDefault($containerName) || isset($this->containers[$containerName]);
    }

    /**
     * @param string $serviceId
     * @param string $containerName
     *
     * @return Definition|Reference
     */
    public function createReferenceFor($serviceId, $containerName = null)
    {
        if ($this->isDefault($containerName)) {
            return new Reference($serviceId);
        }

        if (!$this->isDefined($containerName)) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find container named "%s"',
                $containerName
            ));
        }

        return (new Definition(null, [$serviceId]))->setFactory([
            new Reference($this->containers[$containerName]),
            'get',
        ]);
    }
}
