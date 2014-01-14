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

class ConfigureStep extends ControllerStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        return $this->render(
            'SyliusInstallerBundle:Process/Step:configure.html.twig',
            array('form' => $this->createConfigurationForm()->createView())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $form = $this->createConfigurationForm();

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->get('sylius.installer.yaml_persister')->dump($form->getData());

            return $this->complete();
        }

        return $this->render(
            'SyliusInstallerBundle:Process/Step:configure.html.twig',
            array('form' => $form->createView())
        );
    }

    protected function createConfigurationForm()
    {
        return $this->createForm(
            'sylius_configuration',
            $this->get('sylius.installer.yaml_persister')->parse()
        );
    }
}
