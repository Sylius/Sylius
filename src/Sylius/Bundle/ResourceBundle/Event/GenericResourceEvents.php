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
 * Event names for generic resource events.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class GenericResourceEvents
{
    const SHOW            = 'sylius.resource.show';

    const PRE_CREATE      = 'sylius.resource.pre_create';
    const POST_CREATE     = 'sylius.resource.post_create';

    const PRE_UPDATE      = 'sylius.resource.pre_update';
    const POST_UPDATE     = 'sylius.resource.post_update';

    const PRE_DELETE      = 'sylius.resource.pre_delete';
    const POST_DELETE     = 'sylius.resource.post_delete';

    const PRE_RESTORE     = 'sylius.resource.pre_restore';
    const POST_RESTORE    = 'sylius.resource.post_restore';

    const PRE_REVERT      = 'sylius.resource.pre_revert';
    const POST_REVERT     = 'sylius.resource.post_revert';

    const PRE_MOVE        = 'sylius.resource.pre_move';
    const POST_MOVE       = 'sylius.resource.post_move';

    const PRE_TRANSITION  = 'sylius.resource.pre_transition';
    const POST_TRANSITION = 'sylius.resource.post_transition';
}
