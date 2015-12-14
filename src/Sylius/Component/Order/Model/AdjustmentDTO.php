<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

/**
 * @author  Piotr Walków <walkowpiotr@gmail.com>
 */
class AdjustmentDTO
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $amount;

    /**
     * @var string
     */
    public $description;

    /**
     * @var boolean
     */
    public $neutrality;

    /**
     * @var int
     */
    public $originId;

    /**
     * @var string
     */
    public $originType;
}
