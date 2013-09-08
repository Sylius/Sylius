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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\AddressingBundle\Model\Address;

/**
 * User account address controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressController extends Controller
{
    /**
     * List addresses of the current user
     *
     * @return Response
     */
    public function indexAction()
    {
        $addresses = $this->getUser()->getAddresses();

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Address/index.html.twig',
            array('addresses' => $addresses)
        );
    }

    /**
     * Set an address as default billing address for the current user.
     *
     * @param $id address ID
     * @return RedirectResponse
     */
    public function setAsDefaultBillingAddressAction($id)
    {
        $address = $this->findOr404($id);
        $this->accessOr403($address);

        $manager = $this->getUserManager();
        $user = $this->getUser();

        $user->setBillingAddress($address);
        $manager->persist($user);
        $manager->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('sylius.account.address.flash.billing.success')
        );

        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * Set an address as shipping billing address for the current user.
     *
     * @param $id address ID
     * @return RedirectResponse
     */
    public function setAsDefaultShippingAddressAction($id)
    {
        $address = $this->findOr404($id);
        $this->accessOr403($address);

        $manager = $this->getUserManager();
        $user = $this->getUser();

        $user->setShippingAddress($address);
        $manager->persist($user);
        $manager->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('sylius.account.address.flash.shipping.success')
        );

        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    private function getUserManager()
    {
        return $this->get('sylius.manager.user');
    }

    private function getAddressRepository()
    {
        return $this->get('sylius.repository.address');
    }

    /**
     * Finds address or throws 404
     *
     * @param $id
     * @return Order
     * @throws NotFoundHttpException
     */
    private function findOr404($id)
    {
        if (null === $address = $this->getAddressRepository()->find($id)) {
            throw $this->createNotFoundException('The address does not exist');
        }

        return $address;
    }

    /**
     * Accesses address or throws 403
     *
     * @param AddressInterface $address
     * @throws AccessDeniedException
     */
    private function accessOr403(AddressInterface $address)
    {
        if (!$this->getUser()->hasAddress($address)) {
            throw new AccessDeniedException();
        }

        return;
    }
}
