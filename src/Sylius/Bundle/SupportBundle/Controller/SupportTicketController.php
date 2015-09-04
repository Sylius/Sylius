<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SupportBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Support\SupportTicketTransitions;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class SupportTicketController extends ResourceController
{
    protected $stateMachineGraph = SupportTicketTransitions::GRAPH;
}
