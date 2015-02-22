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
class BulkAction
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
    private $label;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @param string $name
     * @param string $type
     * @param string $label
     * @param array  $options
     */
    public function __construct($name, $type, $label = null, array $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->options = $options;
    }

    /**
     * @param string $name
     * @param array $configuration
     */
    public static function createFromArray($name, array $configuration)
    {
        return new self(
            $name,
            $configuration['type'],
            isset($configuration['label']) ? $configuration['label'] : null,
            isset($configuration['options']) ? $configuration['options'] : array()
        );
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
