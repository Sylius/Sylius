<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Event;

/**
 * Event names for resource events.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceEvents
{
    const SHOW            = 'show';

    const PRE_CREATE      = 'pre_create';
    const POST_CREATE     = 'post_create';

    const PRE_UPDATE      = 'pre_update';
    const POST_UPDATE     = 'post_update';

    const PRE_DELETE      = 'pre_delete';
    const POST_DELETE     = 'post_delete';

    const PRE_RESTORE     = 'pre_restore';
    const POST_RESTORE    = 'post_restore';

    const PRE_REVERT      = 'pre_revert';
    const POST_REVERT     = 'post_revert';

    const PRE_MOVE        = 'pre_move';
    const POST_MOVE       = 'post_move';

    const PRE_TRANSITION  = 'pre_transition';
    const POST_TRANSITION = 'post_transition';
}
