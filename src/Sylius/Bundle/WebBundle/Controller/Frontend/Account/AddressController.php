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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Customer account address controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressController extends FOSRestController
{
    /**
     * Get collection of customer's addresses.
     */
    public function indexAction()
    {
        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Address/index.html.twig')
            ->setData([
                'customer' => $this->getCustomer(),
                'addresses' => $this->getCustomer()->getAddresses(),
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * Create new customer address.
     */
    public function createAction(Request $request)
    {
        $customer = $this->getCustomer();
        $address = $this->getAddressFactory()->createNew();
        $form = $this->getAddressForm($address);

        if ($form->handleRequest($request)->isValid()) {
            $customer->addAddress($address);

            $manager = $this->getCustomerManager();
            $manager->persist($customer);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.address.create');

            return $this->redirectToIndex();
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Address/create.html.twig')
            ->setData([
                'customer' => $this->getCustomer(),
                'form' => $form->createView(),
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * Update existing user address.
     */
    public function updateAction(Request $request, $id)
    {
        $customer = $this->getCustomer();
        $address = $this->findUserAddressOr404($id);
        $form = $this->getAddressForm($address);

        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getCustomerManager();
            $manager->persist($customer);
            $manager->flush();

            $this->addFlash('success', 'sylius.account.address.update');

            return $this->redirectToIndex();
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Address/update.html.twig')
            ->setData([
                'customer' => $this->getCustomer(),
                'address' => $address,
                'form' => $form->createView(),
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * Delete user address.
     */
    public function deleteAction($id)
    {
        $customer = $this->getCustomer();
        $address = $this->findUserAddressOr404($id);

        $customer->removeAddress($address);

        $manager = $this->getCustomerManager();
        $manager->persist($customer);
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

        $customer = $this->getCustomer();

        if ('billing' === $type) {
            $customer->setBillingAddress($address);

            $this->addFlash('success', 'sylius.account.address.set_as_billing');
        } elseif ('shipping' === $type) {
            $customer->setShippingAddress($address);

            $this->addFlash('success', 'sylius.account.address.set_as_shipping');
        } else {
            throw new NotFoundHttpException();
        }

        $manager = $this->getCustomerManager();
        $manager->persist($customer);
        $manager->flush();

        return $this->redirectToIndex();
    }

    protected function addFlash($type, $message)
    {
        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->add($type, $translator->trans($message, [], 'flashes'));
    }

    /**
     * @param AddressInterface $address
     *
     * @return FormInterface
     */
    protected function getAddressForm(AddressInterface $address)
    {
        return $this->get('form.factory')->create('sylius_address', $address);
    }

    /**
     * @return ObjectManager
     */
    private function getCustomerManager()
    {
        return $this->get('sylius.manager.customer');
    }

    /**
     * @return RepositoryInterface
     */
    protected function getAddressRepository()
    {
        return $this->get('sylius.repository.address');
    }

    /**
     * @return FactoryInterface
     */
    protected function getAddressFactory()
    {
        return $this->get('sylius.factory.address');
    }

    protected function redirectToIndex()
    {
        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * Accesses address or throws 403/404
     *
     * @param int $id
     *
     * @return AddressInterface
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    protected function findUserAddressOr404($id)
    {
        if (!$address = $this->getAddressRepository()->find($id)) {
            throw new NotFoundHttpException('Requested address does not exist.');
        }

        if (!$this->getCustomer()->hasAddress($address)) {
            throw new AccessDeniedException();
        }

        return $address;
    }

    /**
     * @return CustomerInterface
     */
    protected function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }
}
