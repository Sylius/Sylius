<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractSyliusResource
{
    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var string|null
     */
    private $interfaceClass;

    /**
     * @var string|null
     */
    private $factoryClass;

    /**
     * @var string|null
     */
    private $controllerClass;

    /**
     * @var string|null
     */
    private $repositoryClass;

    /**
     * @var array
     */
    private $formsClasses = [];

    /**
     * @var array
     */
    private $validationGroups = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string|null $modelClass
     * @param string|null $interfaceClass
     *
     * @throws \InvalidArgumentException If given model class does not implement given interface
     */
    public function __construct($modelClass = null, $interfaceClass = null)
    {
        $this->modelClass = $modelClass;
        $this->interfaceClass = $interfaceClass;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return string|null
     */
    public function getInterfaceClass()
    {
        return $this->interfaceClass;
    }

    /**
     * @return string|null
     */
    public function getFactoryClass()
    {
        return $this->factoryClass;
    }

    /**
     * @param string $factoryClass
     *
     * @return $this
     */
    public function useFactory($factoryClass)
    {
        $this->factoryClass = $factoryClass;

        return $this;
    }

    /**
     * @return $this
     */
    public function useDefaultFactory()
    {
        $this->factoryClass = Factory::class;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @param string $controllerClass
     *
     * @return $this
     */
    public function useController($controllerClass)
    {
        $this->controllerClass = $controllerClass;

        return $this;
    }

    /**
     * @return $this
     */
    public function useDefaultController()
    {
        $this->controllerClass = ResourceController::class;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRepositoryClass()
    {
        return $this->repositoryClass;
    }

    /**
     * @param string $repositoryClass
     *
     * @return $this
     */
    public function useRepository($repositoryClass)
    {
        $this->repositoryClass = $repositoryClass;

        return $this;
    }

    /**
     * @return $this
     */
    public function useDefaultRepository()
    {
        // Should work out of the box, based on selected driver.
        // This method exists in case of further changes.

        return $this;
    }

    /**
     * @return array
     */
    public function getFormsClasses()
    {
        return $this->formsClasses;
    }

    /**
     * @return array
     */
    public function getValidationGroups()
    {
        return $this->validationGroups;
    }

    /**
     * @param string $name
     * @param string $formClass
     * @param array $validationGroups
     *
     * @return $this
     */
    public function addForm($name, $formClass, array $validationGroups = [])
    {
        $name = $name ?: 'default';

        $this->formsClasses[$name] = $formClass;

        if (0 !== count($validationGroups)) {
            $this->validationGroups[$name] = $validationGroups;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}
