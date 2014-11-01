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
 * Abstract association class
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
abstract class Association
{
    /**
     * @var mixed $id
     */
    private $id;

    /**
     * @var AssociationType
     */
    private $type;

    public function __construct(AssociationType $type)
    {
        $this->type = $type;
    }

    public function getId()
    {
        return $this->id;
    }

    abstract function getAssociatedObject();
} 