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

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Security step.
 *
 * If user is not logged in, displays login & registration form.
 * Also guest checkout is possible.
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
        $form = $this->getRegistrationForm();
        $request = $this->getRequest();

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $user = $form->getData();

            $this->saveUser($user);
            $this->authenticateUser($user);

            return $this->complete();
        }

        return $this->renderStep($context, $this->getRegistrationForm());
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
        return $this->get('fos_user.registration.form');
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

    /**
     * @param \Sylius\Bundle\CoreBundle\Entity\User $user
     */
    private function saveUser($user)
    {
        $user->setEnabled(true);
        $this->get('fos_user.user_manager')->updateUser($user);
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }
}
