<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Model for order statuses.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Status implements StatusInterface
{
    /**
     * Id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Status name.
     *
     * @var string
     */
    protected $name;

    /**
     * Position in the status list.
     *
     * @var integer
     */
    protected $position;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementPosition()
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function decrementPosition()
    {
        $this->position--;
    }

}
