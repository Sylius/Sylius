<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Controller\Backend;

use Sylius\Bundle\AddressingBundle\EventDispatcher\Event\FilterAddressEvent;

use Sylius\Bundle\AddressingBundle\EventDispatcher\SyliusAddressingEvents;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Backend address controller.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressController extends ContainerAware
{
    /**
     * Show all paginated addresses.
     */
    public function listAction()
    {
        $addressManager = $this->container->get('sylius_addressing.manager.address');
        $paginator = $addressManager->createPaginator();
        
        $paginator->setCurrentPage($this->container->get('request')->query->get('page', 1), true, true);
        
        $addresses = $paginator->getCurrentPageResults();

        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:list.html.twig', array(
        	'addresses' => $addresses,
            'paginator' => $paginator
        ));
    }

    /**
     * Shows one address.
     */
    public function showAction($id)
    {
        $address = $this->container->get('sylius_addressing.manager.address')->findAddress($id);
        
        if (!$address) {
            throw new NotFoundHttpException('Requested address does not exist.');
        }
        
        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:show.html.twig', array(
        	'address' => $address
        ));
    }

	/**
     * Creating an address.
     */
    public function createAction()
    {
        $address = $this->container->get('sylius_addressing.manager.address')->createAddress();
        
        $form = $this->container->get('form.factory')->create($this->container->get('sylius_addressing.form.type.address'));
        $form->setData($address);
        
        $request = $this->container->get('request');
        
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusAddressingEvents::ADDRESS_CREATE, new FilterAddressEvent($address));
                $this->container->get('sylius_addressing.manipulator.address')->create($address);
                
                return new RedirectResponse($this->container->get('router')->generate('sylius_addressing_backend_address_show', array(
                	'id' => $address->getId()
                )));
            }
        }
        
        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:create.html.twig', array(
            'form'      => $form->createView()
        ));
    }
    
    /**
     * Updating an address.
     */
    public function updateAction($id)
    {
        $address = $this->container->get('sylius_addressing.manager.address')->findAddress($id);

        if (!$address) {
            throw new NotFoundHttpException('Requested address does not exist.');
        }
        
        $form = $this->container->get('form.factory')->create($this->container->get('sylius_addressing.form.type.address'));
        $form->setData($address);
        
        $request = $this->container->get('request');
        
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusAddressingEvents::ADDRESS_UPDATE, new FilterAddressEvent($address));
                $this->container->get('sylius_addressing.manipulator.address')->update($address);
                
                return new RedirectResponse($this->container->get('router')->generate('sylius_addressing_backend_address_show', array(
                	'id' => $address->getId()
                )));
            }
        }
        
        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:update.html.twig', array(
            'form'      => $form->createView(),
            'address'  => $address
        ));
    }

    /**
     * Deletes address.
     */
    public function deleteAction($id)
    {
        $address = $this->container->get('sylius_addressing.manager.address')->findAddress($id);

        if (!$address) {
            throw new NotFoundHttpException('Requested address does not exist.');
        }
        
        $this->container->get('sylius_addressing.manipulator.address')->delete($address);

        return new RedirectResponse($this->container->get('router')->generate('sylius_addressing_backend_address_list'));
    }
}
