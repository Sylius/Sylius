<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\EventListener;

use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;


/**
 * Backend menu listener for job scheduler
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class BackendMenuBuilderListener
{
    /**
     * Adds Job Menu to backend
     *
     * @param MenuBuilderEvent $event
     */
    public function addBackendMenuItems(MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();

        $menu['configuration']->addChild('job_scheduler', array(
            'route'           => 'sylius_backend_job_index',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-align-left'),
        ))->setLabel('sylius.backend.menu.sidebar.jobs');
    }
}