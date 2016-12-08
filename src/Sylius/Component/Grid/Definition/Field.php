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
     * @var boolean
     */
    private $enabled = true;

    /**
     * @var string|null
     */
    private $sortable;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var int
     *
     * Position equals to 100 to ensure that wile sorting fields by position ASC
     * the fields positioned by default will be last
     */
    private $position = 100;

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
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return self
     */
    public static function fromNameAndType($name, $type)
    {
        return new self($name, $type);
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
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param string|null $sortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable ?: $this->name;
    }

    /**
     * @return string|null
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return null !== $this->sortable;
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

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
