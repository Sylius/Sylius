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
     * @param Address $address
     * @return RedirectResponse
     *
     */
    public function setAsDefaultBillingAddressAction(Address $address)
    {
//        $this->accessOr403($address);

        $manager = $this->getUserManager();
        $user = $this->getUser();

        $user->setBillingAddress($address);
        $manager->persist($user);
        $manager->flush();
        $this->setFlash('success', 'set_billing');

        return $this->redirect('sylius_account_address_index');
    }

    public function setAsDefaultShippingAddressAction(Address $address)
    {
//      $this->accessOr403($address);

        $manager = $this->getUserManager();
        $user = $this->getUser();

        $user->setShippingAddress($address);
        $manager->persist($user);
        $manager->flush();
        $this->setFlash('success', 'set_shipping');

        return $this->redirect('sylius_account_address_index');
    }

    private function getUserManager()
    {
        $this->get('sylius.manager.user');
    }

    private function getAddressRepository()
    {
        return $this->get('sylius.repository.address');
    }
}
