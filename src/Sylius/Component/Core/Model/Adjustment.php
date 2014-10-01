<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Adjustment extends BaseAdjustment implements AdjustmentInterface
{
    protected $originId;
    protected $originType;

    public function getOriginId()
    {
        return $this->originId;
    }

    public function setOriginId($originId)
    {
        $this->originId = $originId;

        return $this;
    }

    public function getOriginType()
    {
        return $this->originType;
    }

    public function setOriginType($originType)
    {
        $this->originType = $originType;

        return $this;
    }
}
