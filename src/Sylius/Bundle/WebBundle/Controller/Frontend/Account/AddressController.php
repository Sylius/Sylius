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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\AddressInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User account address controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressController extends Controller
{
    /**
     * Get collection of user's addresses.
     */
    public function indexAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Frontend/Account:Address/index.html.twig', array(
            'addresses' => $this->getUser()->getAddresses()
        ));
    }

    /**
     * Create new user address.
     */
    public function createAction(Request $request)
    {
        $user = $this->getUser();
        $address = $this->getAddressRepository()->createNew();
        $form = $this->getAddressForm($address);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $user->addAddress($address);

            $manager = $this->getUserManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.address.create');

            return $this->redirectToIndex();
        }

        return $this->render('SyliusWebBundle:Frontend/Account:Address/create.html.twig', array(
            'user' => $this->getUser(),
            'form' => $form->createView()
        ));
    }

    /**
     * Update existing user address.
     */
    public function updateAction(Request $request, $id)
    {
        $user = $this->getUser();
        $address = $this->findUserAddressOr404($id);
        $form = $this->getAddressForm($address);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $manager = $this->getUserManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.address.update');

            return $this->redirectToIndex();
        }

        return $this->render('SyliusWebBundle:Frontend/Account:Address/update.html.twig', array(
            'user'    => $this->getUser(),
            'address' => $address,
            'form'    => $form->createView()
        ));
    }

    /**
     * Delete user address.
     */
    public function deleteAction($id)
    {
        $user = $this->getUser();
        $address = $this->findUserAddressOr404($id);

        $user->removeAddress($address);

        $manager = $this->getUserManager();
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'sylius.account.address.delete');

        return $this->redirectToIndex();
    }

    /**
     * Set an address as default billing address for the current user.
     *
     * @return RedirectResponse
     */
    public function setAsBillingAction($id)
    {
        $address = $this->findUserAddressOr404($id);

        $user = $this->getUser();
        $user->setBillingAddress($address);

        $manager = $this->getUserManager();
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'sylius.account.address.set_as_billing');

        return $this->redirectToIndex();
    }

    /**
     * Set an address as shipping billing address for the current user.
     *
     * @return RedirectResponse
     */
    public function setAsShippingAction($id)
    {
        $address = $this->findUserAddressOr404($id);

        $user = $this->getUser();
        $user->setShippingAddress($address);

        $manager = $this->getUserManager();
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'sylius.account.address.set_as_shipping');

        return $this->redirectToIndex();
    }

    private function getAddressForm(AddressInterface $address)
    {
        return $this->get('form.factory')->create('sylius_address', $address);
    }

    /**
     * @return ObjectManager
     */
    private function getUserManager()
    {
        return $this->get('sylius.manager.user');
    }

    /**
     * @return RepositoryInterface
     */
    private function getAddressRepository()
    {
        return $this->get('sylius.repository.address');
    }

    private function redirectToIndex()
    {
        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    private function addFlash($type, $message)
    {
        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->add($type, $translator->trans($message, array(), 'flashes'));
    }

    /**
     * Accesses address or throws 403/404
     *
     * @param integer $id
     *
     * @return AddressInterface
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    private function findUserAddressOr404($id)
    {
        if (!$address = $this->getAddressRepository()->find($id)) {
            throw new NotFoundHttpException('Requested address does not exist.');
        }

        if (!$this->getUser()->hasAddress($address)) {
            throw new AccessDeniedException();
        }

        return $address;
    }
}
