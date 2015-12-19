<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Event;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceControllerEvents
{
    const SHOW = 'show';

    const PRE_CREATE = 'pre_create';
    const POST_CREATE = 'post_create';

    const PRE_UPDATE = 'pre_update';
    const POST_UPDATE = 'post_update';

    const PRE_DELETE = 'pre_delete';
    const POST_DELETE = 'post_delete';
}
