<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup\Step;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class ContainerAwareStep extends Step implements ContainerAwareInterface
{
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function redirectToPrevious()
    {
        if ($this->hasPrevious()) {
            return new RedirectResponse($this->container->get('router')->generate('sylius_flow_step', array(
                'setupAlias' => $this->getSetup()->getAlias(),
                'stepIndex'  => $this->getPrevious()->getIndex()
            )));
        }

        throw new \LogicException('You cannot redirect to previous step from the first step');
    }

    public function redirectToNext()
    {
        if ($this->hasNext()) {
            return new RedirectResponse($this->container->get('router')->generate('sylius_flow_step', array(
                'setupAlias' => $this->getSetup()->getAlias(),
                'stepIndex'  => $this->getNext()->getIndex()
            )));
        }

        throw new \LogicException('You cannot redirect to next step from the last step');
    }
}
