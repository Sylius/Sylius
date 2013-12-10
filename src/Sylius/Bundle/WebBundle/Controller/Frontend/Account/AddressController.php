<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User account address controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressController extends ResourceController
{
    /**
     * Get collection of user's addresses.
     */
    public function indexAction(Request $request)
    {
        $config = $this->getConfiguration();
        $pluralName = $config->getPluralResourceName();
        $addresses = $this->getUser()->getAddresses();

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setTemplateVar($pluralName)
            ->setData($addresses)
        ;

        return $this->handleView($view);
    }

    /**
     * Create new address or just display the form.
     */
    public function createAction(Request $request)
    {
        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->getUser()->addAddress($resource);
            $event = $this->create($resource);

            if (!$event->isStopped()) {
                $this->setFlash('success', 'create');

                return $this->redirectTo($resource);
            }

            $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());
        }

        $config = $this->getConfiguration();
        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('create.html'))
            ->setData(array(
                $config->getResourceName() => $resource,
                'form'                     => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Set an address as default billing address for the current user.
     *
     * @return RedirectResponse
     */
    public function setAsDefaultBillingAddressAction()
    {
        $address = $this->findOr404();
        $this->accessOr403($address);

        $manager = $this->getUserManager();
        $user = $this->getUser();

        $user->setBillingAddress($address);
        $manager->persist($user);
        $manager->flush();

        $this->setFlash('success', $this->get('translator')->trans('sylius.account.address.flash.billing.success'));

        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * Set an address as shipping billing address for the current user.
     *
     * @return RedirectResponse
     */
    public function setAsDefaultShippingAddressAction()
    {
        $address = $this->findOr404();
        $this->accessOr403($address);

        $manager = $this->getUserManager();
        $user = $this->getUser();

        $user->setShippingAddress($address);
        $manager->persist($user);
        $manager->flush();

        $this->setFlash('success', $this->get('translator')->trans('sylius.account.address.flash.shipping.success'));

        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * @return object
     */
    private function getUserManager()
    {
        return $this->get('sylius.manager.user');
    }

    /**
     * Accesses address or throws 403
     *
     * @param AddressInterface $address
     *
     * @throws AccessDeniedException
     */
    private function accessOr403(AddressInterface $address)
    {
        if (!$this->getUser()->hasAddress($address)) {
            throw new AccessDeniedException();
        }
    }
}
