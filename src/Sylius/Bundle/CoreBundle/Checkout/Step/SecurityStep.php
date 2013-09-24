<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security step.
 *
 * If user is not logged in, displays login & registration form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SecurityStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        // If user is already logged in, transparently jump to next step.
        if ($this->isUserLoggedIn()) {
            return $this->complete();
        }

        $this->overrideSecurityTargetPath();

        return $this->renderStep($context, $this->getRegistrationForm());
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $form = $this->getRegistrationForm();
        $form->setData($user);

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, new Response()));

            return $this->complete();
        }

        return $this->renderStep($context, $form);
    }

    /**
     * Render step.
     *
     * @param ProcessContextInterface $context
     * @param FormInterface           $registrationForm
     */
    private function renderStep(ProcessContextInterface $context, FormInterface $registrationForm)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:security.html.twig', array(
            'context'           => $context,
            'registration_form' => $registrationForm->createView(),
        ));
    }

    /**
     * Get registration form.
     *
     * @return FormInterface
     */
    private function getRegistrationForm()
    {
        return $this->get('fos_user.registration.form.factory')->createForm();
    }

    /**
     * Override security target path, it will redirect user to checkout after login.
     */
    private function overrideSecurityTargetPath()
    {
        $url = $this->generateUrl('sylius_checkout_security', array(), true);
        $providerKey = $this->container->getParameter('fos_user.firewall_name');

        $this->get('session')->set('_security.'.$providerKey.'.target_path', $url);
    }
}
