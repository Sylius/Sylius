<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Context;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Sylius was not able to figure out the current cart.', 0, $previousException);
    }
}
