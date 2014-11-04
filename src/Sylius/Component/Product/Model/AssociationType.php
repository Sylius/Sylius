<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

/**
 * AssociationType model.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class AssociationType
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var null|\DateTime
     */
    protected $deletedAt;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if (null === $name || !trim(str_replace(array("\n", "\t"), '', $name))) {
            throw new \InvalidArgumentException('Association type name cannot be empty.');
        }
        $this->name = $name;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
