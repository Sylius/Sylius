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

/**
 * The billing address step of checkout.
 * User enters the shipping and shipping address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BillingStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $cart = $this->getCurrentCart();
        if (!$cart->getDifferentBillingAddress()) return $this->complete();
        
        $manager = $this->getManager();
        $cart = $this->getCurrentCart();
        $cart->setBillingAddress(null); //This is to avoid previous addresses modification
        $manager->flush();

        $addresses = $manager->getRepository('SyliusCoreBundle:Address')->findBy(array('user' => $this->getUser()));
        $useForms = $this->createUseForms($addresses);
        $form = $this->createCheckoutBillingForm();

        return $this->renderStep($context, $form, $addresses, $useForms);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        if ($request->query->has('id'))
        {
            $id = $request->get('id');
            $form = $this->createUseForm($id);
            $form->bind($request);
    
            if ($request->isMethod('POST') && $form->isValid()) {
                $em = $this->getManager();
                $entity = $em->getRepository('SyliusCoreBundle:Address')->find($id);
                
                if ($entity->getUser()!=$this->getUser()) throw new \Exception('Wrong address.');
                
                $cart = $this->getCurrentCart();
                $cart->setBillingAddress($entity);
                $em->flush();
                
                return $this->complete();
            }
            return $this->redirect($this->generateUrl('sylius_checkout_billing'));
        }
        else
        {
            $manager = $this->getManager();
            $rep = $manager->getRepository('SyliusCoreBundle:Address');
            $addresses = $rep->findBy(array('user' => $this->getUser()));
            $useForms = $this->createUseForms($addresses);
                        
            $form = $this->createCheckoutBillingForm();
            
            if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
                
                $cart = $this->getCurrentCart();
                $user = $this->getUser();
                $cart->getBillingAddress()->setUser($user);
    
                $this->getManager()->persist($cart);
                $this->getManager()->flush();
    
                return $this->complete();
            }
        }
        
        return $this->renderStep($context, $form, $addresses, $useForms);
    }

    private function renderStep(ProcessContextInterface $context, FormInterface $form, $addresses, $useForms)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:billing.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context,
            'addresses' => $addresses,
            'useForms' => $useForms
        ));

    }

    private function createCheckoutBillingForm()
    {
        return $this->createForm('sylius_checkout_billing', $this->getCurrentCart());
    }
    
    private function createUseForms($addresses)
    {
        $useForms=array();
        foreach ($addresses as $address)
        {
            $useForms[$address->getId()] = $this->createUseForm($address->getId())->createView();
        }
        
        return $useForms;
    }
    
    private function createUseForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
