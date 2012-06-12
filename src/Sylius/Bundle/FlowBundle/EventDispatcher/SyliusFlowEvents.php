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
    const PROCESS_START = 'sylius_flow.event.process.start';
    const STEP_DISPLAY  = 'sylius_flow.event.step.display';
    const STEP_FORWARD  = 'sylius_flow.event.step.forward';
}
