<?php

namespace Sylius\Component\Order\Model;

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
