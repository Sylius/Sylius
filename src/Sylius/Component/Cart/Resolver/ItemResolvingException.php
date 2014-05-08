<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Resolver;

/**
 * Exception which should be thrown by item resolver if anything failed.
 * The message should be displayed to user.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ItemResolvingException extends \InvalidArgumentException
{
}
