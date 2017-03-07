<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Exception;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ChannelNotDefinedException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message, \Exception $previousException = null)
    {
        parent::__construct($message, 0, $previousException);
    }
}
