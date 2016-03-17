<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\MultiContainerExtension\Context\Environment;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\ContextEnvironment;
use Behat\Behat\Context\Exception\ContextNotFoundException;
use Behat\Behat\Context\Exception\WrongContextClassException;
use Behat\Testwork\Environment\StaticEnvironment;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class UninitializedContextServiceEnvironment extends StaticEnvironment implements ContextEnvironment
{
    /**
     * @var array[]
     */
    private $contextServices = [];

    /**
     * @param string $serviceId
     * @param string $contextClass
     */
    public function registerContextClass($serviceId, $contextClass)
    {
        if (!class_exists($contextClass)) {
            throw new ContextNotFoundException(sprintf(
                '`%s` context class not found and can not be used.',
                $contextClass
            ), $contextClass);
        }

        $reflClass = new \ReflectionClass($contextClass);

        if (!$reflClass->implementsInterface(Context::class)) {
            throw new WrongContextClassException(sprintf(
                'Every context class must implement Behat Context interface, but `%s` does not.',
                $contextClass
            ), $contextClass);
        }

        $this->contextServices[$serviceId] = $contextClass;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContexts()
    {
        return count($this->contextServices) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextClasses()
    {
        return array_values($this->contextServices);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContextClass($class)
    {
        return array_search($class, $this->contextServices, true);
    }

    /**
     * @return array[]
     */
    public function getContextsServicesIds()
    {
        return array_keys($this->contextServices);
    }
}
