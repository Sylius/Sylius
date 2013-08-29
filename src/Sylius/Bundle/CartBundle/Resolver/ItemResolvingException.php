<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Resolver;

/**
 * Exception which should be thrown by item resolver if anything failed.
 * The message should be displayed to user.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ItemResolvingException extends \Exception
{
}
