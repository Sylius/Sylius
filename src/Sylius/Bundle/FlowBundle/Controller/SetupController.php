<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Controller;

use Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterSetupEvent;
use Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterStepEvent;
use Sylius\Bundle\FlowBundle\EventDispatcher\SyliusFlowEvents;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Setups controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SetupController extends ContainerAware
{
    /**
     * Start flow with given alias.
     *
     * @param string $setupAlias
     *
     * @return Response
     */
    public function startAction($setupAlias)
    {
    }


    /**
     * Start flow with given alias.
     *
     * @param string  $setupAlias
     * @param integer $stepIndex
     *
     * @return Response
     */
    public function stepAction($setupAlias, $stepIndex)
    {
    }

    /**
     * Complete flow with given alias.
     *
     * @param string $setupAlias
     *
     * @return Response
     */
    public function complete($setupAlias)
    {
    }
}
