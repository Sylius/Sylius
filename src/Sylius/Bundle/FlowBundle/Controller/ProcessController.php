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

use Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterProcessEvent;
use Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterStepEvent;
use Sylius\Bundle\FlowBundle\EventDispatcher\SyliusFlowEvents;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Process controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProcessController extends ContainerAware
{
    /**
     * Start flow with given alias.
     *
     * @param string $processAlias
     *
     * @return Response
     */
    public function startAction($processAlias)
    {
    }

    /**
     * Start flow with given alias.
     *
     * @param string  $processAlias
     * @param integer $stepIndex
     *
     * @return Response
     */
    public function stepAction($processAlias, $stepIndex)
    {
    }
}
