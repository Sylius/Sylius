<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewController extends ResourceController
{
    /**
     * @var string
     */
    protected $stateMachineGraph = ReviewInterface::REVIEW_STATE_MACHINE_GRAPH;
}
