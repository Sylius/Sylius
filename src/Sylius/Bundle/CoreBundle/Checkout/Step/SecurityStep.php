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
use Sylius\Bundle\FlowBundle\Process\Step\ActionResult;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SecurityStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        // If user is already logged in, transparently jump to next step.
        if ($this->isUserLoggedIn()) {
            return $this->processUserLoggedIn($order);
        }
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_INITIALIZE, $order);

        $this->overrideSecurityTargetPath();

        return $this->renderStep($context, $this->getRegistrationForm(), $this->getGuestForm());
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_INITIALIZE, $order);

        $request = $context->getRequest();
        $guestForm = $this->getGuestForm();
        $registrationForm = $this->getRegistrationForm();

        if ($this->isGuestOrderAllowed() && $guestForm->handleRequest($request)->isValid()) {
            return $this->processGuestOrder($guestForm, $order);
        } elseif ($registrationForm->handleRequest($request)->isValid()) {
            return $this->processRegistration($registrationForm, $order);
        }

        return $this->renderStep($context, $registrationForm, $guestForm);
    }

    /**
     * {@inheritdoc}
     */
    public function complete()
    {
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_COMPLETE, $this->getCurrentCart());

        return parent::complete();
    }

    /**
     * @param ProcessContextInterface $context
     * @param FormInterface $registrationForm
     * @param null|FormInterface $guestForm
     *
     * @return Response
     */
    protected function renderStep(ProcessContextInterface $context, FormInterface $registrationForm, FormInterface $guestForm = null)
    {
        return $this->render($this->container->getParameter(sprintf('sylius.checkout.step.%s.template', $this->getName())), [
            'context' => $context,
            'registration_form' => $registrationForm->createView(),
            'guest_form' => null !== $guestForm ? $guestForm->createView() : null,
        ]);
    }

    /**
     * @return FormInterface
     */
    protected function getRegistrationForm()
    {
        /** @var CustomerInterface $customer */
        $customer = $this->get('sylius.factory.customer')->createNew();

        $form = $this->createForm('sylius_customer_registration', $customer);
        $form->setData($customer);

        return $form;
    }

    /**
     * @return FormInterface|null
     */
    protected function getGuestForm()
    {
        if (!$this->isGuestOrderAllowed()) {
            return null;
        }
        /** @var CustomerInterface $customer */
        $customer = $this->get('sylius.factory.customer')->createNew();

        return $this->createForm('sylius_customer_guest', $customer);
    }

    /**
     * @return bool
     */
    protected function isGuestOrderAllowed()
    {
        return $this->container->getParameter('sylius.order.allow_guest_order');
    }

    /**
     * Override security target path, it will redirect user to checkout after login.
     */
    protected function overrideSecurityTargetPath()
    {
        $this->get('session')->set('_security.main.target_path', $this->generateUrl('sylius_checkout_security', [], true));
    }

    /**
     * @param FormInterface $guestForm
     * @param OrderInterface $order
     *
     * @return ActionResult
     */
    protected function processGuestOrder(FormInterface $guestForm, OrderInterface $order)
    {
        $customer = $guestForm->getData();
        $order->setCustomer($customer);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_PRE_COMPLETE, $order);
        $this->saveResource($order);
        $this->get('session')->set('sylius_customer_guest_id', $customer->getId());

        return $this->complete();
    }

    /**
     * @param FormInterface  $registrationForm
     * @param OrderInterface $order
     *
     * @return ActionResult
     */
    protected function processRegistration(FormInterface $registrationForm, OrderInterface $order)
    {
        $this->registerUser($registrationForm);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_PRE_COMPLETE, $order);
        $this->saveResource($order);

        return $this->complete();
    }

    /**
     * @param OrderInterface $order
     *
     * @return ActionResult
     */
    protected function processUserLoggedIn(OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SECURITY_PRE_COMPLETE, $order);
        $this->saveResource($order);

        return $this->complete();
    }

    /**
     * @param FormInterface $registrationForm
     *
     * @return UserInterface
     */
    protected function registerUser(FormInterface $registrationForm)
    {
        $customer = $registrationForm->getData();
        $this->dispatchEvent('sylius.customer.pre_register', new ResourceEvent($customer));
        $this->saveResource($customer);
        $this->dispatchEvent('sylius.customer.post_register', new ResourceEvent($customer));
    }

    /**
     * @param ResourceInterface $resource
     */
    protected function saveResource($resource)
    {
        $this->getManager()->persist($resource);
        $this->getManager()->flush();
    }
}
