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
use Symfony\Component\HttpFoundation\Response;

/**
 * @todo better class name
 */
class AdminSecurityStep extends SecurityStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
//        $order = $this->getCurrentCart();
//        if ($order) {
//            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_INITIALIZE, $order);
//        }

        return $this->renderStep($context, $this->getRegistrationForm());
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
//        $order = $this->getCurrentCart();
//        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_INITIALIZE, $order);

        $request = $this->getRequest();

        $user = $this->get('fos_user.user_manager')->createUser();
        $user->setEnabled(true);

        $form = $this->getRegistrationForm();
        $form->setData($user);

        $this->dispatchEvent(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->dispatchEvent(FOSUserEvents::REGISTRATION_SUCCESS, new FormEvent($form, $request));

            $this->saveUser($user);

            $this->dispatchEvent(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, new Response()));

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
        return $this->render('SyliusWebBundle:Backend/Checkout/Step:security.html.twig', array(
            'context'           => $context,
            'registration_form' => $registrationForm->createView(),
        ));
    }
}