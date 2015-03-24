<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * Action model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Action implements ActionInterface
{
    /**
     * The id of this action.
     *
     * @var int
     */
    protected $id;

    /**
     * The type of this action.
     *
     * @var string
     */
    protected $type;

    /**
     * The configuration of this action.
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * Get id.
     *
     * @return int
     */
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
}
