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
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security step.
 *
 * If user is not logged in, displays login & registration form.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
            $this->saveUser($this->getUser());

            return $this->complete();
        }

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_INITIALIZE, $order);

        $this->overrideSecurityTargetPath();

        return $this->renderStep($context, $this->getRegistrationForm());
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_INITIALIZE, $order);

        $request = $this->getRequest();

        $user = $this->get('fos_user.user_manager')->createUser();
        $user->setEnabled(true);

        $form = $this->getRegistrationForm();
        $form->setData($user);

        $this->dispatchEvent(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));

        if ($form->handleRequest($request)->isValid()) {
            $this->dispatchEvent(FOSUserEvents::REGISTRATION_SUCCESS, new FormEvent($form, $request));
            $this->dispatchEvent(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, new Response()));

            $this->saveUser($user);

            return $this->complete();
        }

        return $this->renderStep($context, $form);
    }

    /**
     * Render step.
     *
     * @param ProcessContextInterface $context
     * @param FormInterface           $registrationForm
     *
     * @return Response
     */
    protected function renderStep(ProcessContextInterface $context, FormInterface $registrationForm)
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
    protected function getRegistrationForm()
    {
        return $this->get('fos_user.registration.form.factory')->createForm();
    }

    /**
     * Override security target path, it will redirect user to checkout after login.
     */
    protected function overrideSecurityTargetPath()
    {
        $providerKey = $this->container->getParameter('fos_user.firewall_name');

        $this->get('session')->set('_security.'.$providerKey.'.target_path', $this->generateUrl('sylius_checkout_security', array(), true));
    }

    /**
     * Dispatch security events, update user and flush
     *
     * @param UserInterface $user
     */
    protected function saveUser(UserInterface $user)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_PRE_COMPLETE, $order);

        $this->get('fos_user.user_manager')->updateUser($user, true);

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_COMPLETE, $order);
    }
}
