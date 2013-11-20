<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

/**
 * Shipping method rule model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Rule implements RuleInterface
{
    /**
     * Shipping rule identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Rule type.
     *
     * @var string
     */
    protected $type;

    /**
     * All extra configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * @var ShippingMethodInterface
     */
    protected $method;

    public function __construct()
    {
        $this->configuration = array();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod(ShippingMethodInterface $method = null)
    {
        $this->method = $method;

        return $this;
    }
}
