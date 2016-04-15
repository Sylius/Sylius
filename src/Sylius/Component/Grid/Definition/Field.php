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
class Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $sortable = true;

    /**
     * @var string
     */
    private $sortingPath;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $name
     * @param string $type
     */
    private function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;

        $this->path = $name;
        $this->label = $name;
        $this->sortable = $name;
        $this->sortingPath = $name;
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return Field
     */
    public static function fromNameAndType($name, $type)
    {
        return new Field($name, $type);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return false !== $this->sortable;
    }

    /**
     * @param bool $sortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = (bool) $sortable;
    }

    /**
     * @return string
     */
    public function getSortingPath()
    {
        return $this->sortingPath;
    }

    /**
     * @param string $sortingPath
     */
    public function setSortingPath($sortingPath)
    {
        $this->sortingPath = $sortingPath;
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
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
