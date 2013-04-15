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

class SetupStep extends ControllerStep
{
    public function displayAction(ProcessContextInterface $context)
    {
        return $this->render(
            'SyliusInstallerBundle:Process/Step:setup.html.twig',
            array('form' => $this->createForm('sylius_setup')->createView())
        );
    }

    public function forwardAction(ProcessContextInterface $context)
    {
        $form = $this->createForm('sylius_setup');

        if ($this->getRequest()->isMethod('POST') && $form->bind($this->getRequest())->isValid()) {
            if ($form->get('load_fixtures')->getData()) {
                // TODO: load fixtures
            }

            $user = $form->getData();
            $user->setEnabled(true);
            $user->setRoles(array('ROLE_SYLIUS_ADMIN'));

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->add('success', $this->get('translator')->trans('sylius.flashes.installed'))
            ;

            return $this->complete();
        }

        return $this->render(
            'SyliusInstallerBundle:Process/Step:setup.html.twig',
            array('form' => $form->createView())
        );
    }
}
