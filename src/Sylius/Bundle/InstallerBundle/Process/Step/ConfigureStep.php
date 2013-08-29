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
    public function displayAction(ProcessContextInterface $context)
    {
        return $this->render(
            'SyliusInstallerBundle:Process/Step:configure.html.twig',
            array('form' => $this->createConfigurationForm()->createView())
        );
    }

    public function forwardAction(ProcessContextInterface $context)
    {
        $form = $this->createConfigurationForm();

        if ($this->getRequest()->isMethod('POST') && $form->bind($this->getRequest())->isValid()) {
            $data = $form->getData();

            $this->get('sylius.installer.yaml_persister')->dump($data);

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
