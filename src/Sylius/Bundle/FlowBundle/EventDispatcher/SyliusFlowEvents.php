<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\EventDispatcher;

/**
 * Events.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class SyliusFlowEvents
{
    const SETUP_START     = 'sylius_flow.event.setup.build';
    const SETUP_COMPLETE  = 'sylius_flow.event.setup.build';
    const STEP_EXECUTE    = 'sylius_flow.event.step.execute';
}
