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
 * The addressing step of checkout.
 * User enters the shipping and shipping address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AddressingStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $manager = $this->getManager();
        $cart = $this->getCurrentCart();
        $cart->setShippingAddress(null); //This is to avoid previous addresses modification
        $cart->setBillingAddress(null); //This is to avoid previous addresses modification
        $manager->flush();
        
        $addresses = $manager->getRepository('SyliusCoreBundle:Address')->findBy(array('user' => $this->getUser()));
        $unlinkForms = $this->createUnlinkForms($addresses);
        $useForms = $this->createUseForms($addresses);
        $form = $this->createCheckoutAddressingForm();

        return $this->renderStep($context, $form, $addresses, $unlinkForms, $useForms);
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
                
                if ($entity->getUser()!=$this->getUser()) throw new \Exception('Wrong user.');
                
                $cart = $this->getCurrentCart();
                $cart->setShippingAddress($entity);
                $cart->setDifferentBillingAddress(true); //When a previous address is used for shipping, this forces to go through the Billing address step - To Do: add checkbox "use same billing address" for each previous addresses?
                $em->flush();
                
                return $this->complete();
            }
            return $this->redirect($this->generateUrl('sylius_checkout_addressing'));
        }
        else
        {
            $manager = $this->getManager();
            $rep = $manager->getRepository('SyliusCoreBundle:Address');
            $addresses = $rep->findBy(array('user' => $this->getUser()));
            $unlinkForms = $this->createUnlinkForms($addresses);
            $useForms = $this->createUseForms($addresses);
                        
            $form = $this->createCheckoutAddressingForm();
            
            if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
                
                $cart = $this->getCurrentCart();
                $user = $this->getUser();
                $cart->getShippingAddress()->setUser($user);
                
                if (!$cart->getDifferentBillingAddress())
                {
                    $cart->setBillingAddress($cart->getShippingAddress());
                }
                else $cart->setBillingAddress(null);
                
                $manager->persist($cart);
                $manager->flush();
    
                return $this->complete();
            }
        }
        
        return $this->renderStep($context, $form, $addresses, $unlinkForms, $useForms);
    }

    private function renderStep(ProcessContextInterface $context, FormInterface $form, $addresses, $unlinkForms, $useForms)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:addressing.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context,
            'addresses' => $addresses,
            'unlinkForms' => $unlinkForms,
            'useForms' => $useForms
        ));

    }

    private function createCheckoutAddressingForm()
    {
        return $this->createForm('sylius_checkout_addressing', $this->getCurrentCart());
    }

    private function createUnlinkForms($addresses)
    {
        $unlinkForms=array();
        foreach ($addresses as $address)
        {
            $unlinkForms[$address->getId()] = $this->createUnlinkForm($address->getId())->createView();
        }
        
        return $unlinkForms;
    }
    
    private function createUnlinkForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
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
