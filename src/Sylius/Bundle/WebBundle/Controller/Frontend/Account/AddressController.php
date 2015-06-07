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
use Sylius\Bundle\WebBundle\Controller\WebController;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Customer account address controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressController extends WebController
{
    /**
     * Get collection of customer's addresses.
     *
     * @return Response
     */
    public function indexAction()
    {
        $view = $this
            ->view()
            ->setTemplate($this->getTemplate('frontend_address_index'))
            ->setData(array(
                'customer'  => $this->getCustomer(),
                'addresses' => $this->getCustomer()->getAddresses(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Create new customer address.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $customer = $this->getCustomer();
        $address = $this->getAddressRepository()->createNew();
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
            ->setTemplate($this->getTemplate('frontend_address_create'))
            ->setData(array(
                'customer' => $this->getCustomer(),
                'form' => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Update existing user address.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request)
    {
        $id = $request->get('id');
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
            ->setTemplate($this->getTemplate('frontend_address_update'))
            ->setData(array(
                'customer'    => $this->getCustomer(),
                'address' => $address,
                'form'    => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Delete user address.
     *
     * @param $id
     *
     * @return RedirectResponse
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
     * Accesses address or throws 403/404
     *
     * @param integer $id
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
}
