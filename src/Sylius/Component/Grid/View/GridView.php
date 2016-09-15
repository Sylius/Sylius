<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridView
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var Grid
     */
    private $definition;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @param mixed $data
     * @param Grid $definition
     * @param Parameters $parameters
     */
    public function __construct($data, Grid $definition, Parameters $parameters)
    {
        $this->data = $data;
        $this->definition = $definition;
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Grid
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return Parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function isSortableBy($fieldName)
    {
        $this->assertFieldExists($fieldName);

        return array_key_exists($fieldName, $this->getDefinition()->getSorting());
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function getSortingOrder($fieldName)
    {
        $this->assertFieldExists($fieldName);
        Assert::true($this->isSortableBy($fieldName), sprintf('Field "%s" is not sortable.', $fieldName));

        return $this->getCurrentlySortedBy()[$fieldName]['direction'];
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function isSortedBy($fieldName)
    {
        $this->assertFieldExists($fieldName);
        Assert::true($this->isSortableBy($fieldName), sprintf('Field "%s" is not sortable.', $fieldName));

        if ($this->parameters->has('sorting')) {
            return array_key_exists($fieldName, $this->parameters->get('sorting'));
        }

        $sortingDefinition = $this->getDefinition()->getSorting();
        $sortedFields = array_keys($sortingDefinition);

        return $fieldName === array_shift($sortedFields);
    }

    /**
     * @return array|mixed
     */
    private function getCurrentlySortedBy()
    {
        return $this->parameters->has('sorting')
            ? array_merge($this->definition->getSorting(), $this->parameters->get('sorting'))
            : $this->definition->getSorting()
        ;
    }

    /**
     * @param string $fieldName
     *
     * @throws \InvalidArgumentException
     */
    private function assertFieldExists($fieldName)
    {
        Assert::true($this->definition->hasField($fieldName), sprintf('Field "%s" does not exist.', $fieldName));
    }
}
