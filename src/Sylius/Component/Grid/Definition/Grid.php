<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Definition;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Grid
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var array
     */
    private $driverConfiguration;

    /**
     * @var array
     */
    private $sorting = array();

    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var array
     */
    private $actionGroups = array();

    /**
     * @param string $code
     * @param string $driver
     * @param array $driverConfiguration
     */
    private function __construct($code, $driver, array $driverConfiguration)
    {
        $this->code = $code;
        $this->driver = $driver;
        $this->driverConfiguration = $driverConfiguration;
    }

    /**
     * @param string $code
     * @param string $driver
     * @param array $driverConfiguration
     *
     * @return Grid
     */
    public static function fromCodeAndDriverConfiguration($code, $driver, array $driverConfiguration)
    {
        $grid = new Grid($code, $driver, $driverConfiguration);

        return $grid;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return array
     */
    public function getDriverConfiguration()
    {
        return $this->driverConfiguration;
    }

    /**
     * @return array
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param array $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        if ($this->hasField($name = $field->getName())) {
            throw new \InvalidArgumentException(sprintf('Field "%s" already exists.', $name));
        }

        $this->fields[$name] = $field;
    }

    /**
     * @param string $name
     */
    public function getField($name)
    {
        if (!$this->hasField($name)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    /**
     * @param string $name
     */
    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * @return array
     */
    public function getActionGroups()
    {
        return $this->actionGroups;
    }

    /**
     * @param ActionGroup $actionGroup
     */
    public function addActionGroup(ActionGroup $actionGroup)
    {
        if ($this->hasActionGroup($name = $actionGroup->getName())) {
            throw new \InvalidArgumentException(sprintf('ActionGroup "%s" already exists.', $name));
        }

        $this->actionGroups[$name] = $actionGroup;
    }

    /**
     * @param string $name
     */
    public function getActionGroup($name)
    {
        if (!$this->hasActionGroup($name)) {
            throw new \InvalidArgumentException(sprintf('ActionGroup "%s" does not exist.', $name));
        }

        return $this->actionGroups[$name];
    }

    /**
     * @param string $name
     */
    public function hasActionGroup($name)
    {
        return array_key_exists($name, $this->actionGroups);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        if ($this->hasFilter($name = $filter->getName())) {
            throw new \InvalidArgumentException(sprintf('Filter "%s" already exists.', $name));
        }

        $this->filters[$name] = $filter;
    }

    /**
     * @param string $name
     *
     * @return Filter
     */
    public function getFilter($name)
    {
        if (!$this->hasFilter($name)) {
            throw new \InvalidArgumentException(sprintf('Filter "%s" does not exist.', $name));
        }

        return $this->filters[$name];
    }

    /**
     * @param string $name
     */
    public function hasFilter($name)
    {
        return array_key_exists($name, $this->filters);
    }
}
