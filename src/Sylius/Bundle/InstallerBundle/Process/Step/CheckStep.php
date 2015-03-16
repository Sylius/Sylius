<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Process\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ControllerStep;
use Symfony\Component\HttpFoundation\Request;

class CheckStep extends ControllerStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context, Request $request)
    {
        return $this->render(
            'SyliusInstallerBundle:Process/Step:check.html.twig',
            array('collections' => $this->get('sylius.requirements'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context, Request $request)
    {
        return $this->complete();
    }
}
