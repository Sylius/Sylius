<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Support;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class SupportTicketTransitions
{
    const GRAPH = 'sylius_support_ticket';

    const SYLIUS_REPLY   = 'reply';
    const SYLIUS_RESOLVE = 'resolve';
    const SYLIUS_CLOSE   = 'close';
    const SYLIUS_OPEN   = 'open';
}
