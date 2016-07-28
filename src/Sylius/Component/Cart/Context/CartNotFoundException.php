<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Context;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Exception $previousException = null)
    {
        parent::__construct('Sylius was not able to figure out the current cart.', 0, $previousException);
    }
}
