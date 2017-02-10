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

use Sylius\Component\Order\Model\OrderSequence as BaseOrderSequence;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderSequence extends BaseOrderSequence implements OrderSequenceInterface
{
    /**
     * @var int
     */
    protected $version = 1;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }
}
