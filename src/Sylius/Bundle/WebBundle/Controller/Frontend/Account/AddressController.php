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
use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User account address controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressController extends FOSRestController
{
    /**
     * Get collection of user's addresses.
     */
    public function indexAction(Request $request)
    {
        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Address/index.html.twig')
            ->setData(array(
                'addresses' => $this->getUser()->getAddresses(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Create new user address.
     */
    public function createAction(Request $request)
    {
        $user = $this->getUser();
        $address = $this->getAddressRepository()->createNew();
        $form = $this->getAddressForm($address);

        if ($form->handleRequest($request)->isValid()) {
            $user->addAddress($address);

            $manager = $this->getUserManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.address.create');

            return $this->redirectToIndex();
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Address/create.html.twig')
            ->setData(array(
                'user' => $this->getUser(),
                'form' => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Update existing user address.
     */
    public function updateAction(Request $request, $id)
    {
        $user = $this->getUser();
        $address = $this->findUserAddressOr404($id);
        $form = $this->getAddressForm($address);

        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getUserManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.address.update');

            return $this->redirectToIndex();
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Address/update.html.twig')
            ->setData(array(
                'user'    => $this->getUser(),
                'address' => $address,
                'form'    => $form->createView()
            ))
        ;

        return $this->handleView($view);
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
     * Set an address as default billing/shipping address for the current user.
     *
     * @param int    $id
     * @param string $type
     *
     * @return RedirectResponse
     *
     * @throws NotFoundHttpException
     */
    public function setAddressAsAction($id, $type)
    {
        $address = $this->findUserAddressOr404($id);

        $user = $this->getUser();

        if ('billing' === $type) {
            $user->setBillingAddress($address);

            $this->addFlash('success', 'sylius.account.address.set_as_billing');
        } elseif ('shipping' === $type) {
            $user->setShippingAddress($address);

            $this->addFlash('success', 'sylius.account.address.set_as_shipping');
        } else {
            throw new NotFoundHttpException();
        }

        $manager = $this->getUserManager();
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToIndex();
    }

    protected function addFlash($type, $message)
    {
        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->add($type, $translator->trans($message, array(), 'flashes'));
    }

    /**
     * @param AddressInterface $address
     *
     * @return FormInterface
     */
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
