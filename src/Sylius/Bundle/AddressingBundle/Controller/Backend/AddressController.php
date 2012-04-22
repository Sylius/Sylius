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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Backend address controller.
 * All administrator related actions are here.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressController extends ContainerAware
{
    /**
     * Show all paginated addresses.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $addressManager = $this->container->get('sylius_addressing.manager.address');
        $addressSorter = $this->container->get('sylius_addressing.sorter.address');

        $paginator = $addressManager->createPaginator($addressSorter);
        $paginator->setCurrentPage($request->query->get('page', 1), true, true);

        $addresses = $paginator->getCurrentPageResults();

        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:list.html.'.$this->getEngine(), array(
            'addresses' => $addresses,
            'paginator' => $paginator,
            'sorter'    => $addressSorter
        ));
    }

    /**
     * Shows one address.
     *
     * @param mixed $id The address identifier
     *
     * @return Response
     */
    public function showAction($id)
    {
        $address = $this->findAddressOr404($id);

        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:show.html.'.$this->getEngine(), array(
            'address' => $address
        ));
    }

    /**
     * Creating an address.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $address = $this->container->get('sylius_addressing.manager.address')->createAddress();
        $form = $this->container->get('form.factory')->create('sylius_addressing_address', $address);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusAddressingEvents::ADDRESS_CREATE, new FilterAddressEvent($address));
                $this->container->get('sylius_addressing.manipulator.address')->create($address);

                return new RedirectResponse($this->container->get('router')->generate('sylius_addressing_backend_address_show', array(
                    'id' => $address->getId()
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:create.html.'.$this->getEngine(), array(
            'form' => $form->createView()
        ));
    }

    /**
     * Updating an address.
     *
     * @param Request $request
     * @param mixed   $id      The address identifier
     *
     * @return Response
     */
    public function updateAction(Request $request, $id)
    {
        $address = $this->findAddressOr404($id);
        $form = $this->container->get('form.factory')->create('sylius_addressing_address', $address);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusAddressingEvents::ADDRESS_UPDATE, new FilterAddressEvent($address));
                $this->container->get('sylius_addressing.manipulator.address')->update($address);

                return new RedirectResponse($this->container->get('router')->generate('sylius_addressing_backend_address_show', array(
                    'id' => $address->getId()
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusAddressingBundle:Backend/Address:update.html.'.$this->getEngine(), array(
            'form'    => $form->createView(),
            'address' => $address
        ));
    }

    /**
     * Deletes address.
     *
     * @param mixed $id The address identifier
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $address = $this->findAddressOr404($id);

        $this->container->get('event_dispatcher')->dispatch(SyliusAddressingEvents::ADDRESS_DELETE, new FilterAddressEvent($address));
        $this->container->get('sylius_addressing.manipulator.address')->delete($address);

        return new RedirectResponse($this->container->get('router')->generate('sylius_addressing_backend_address_list'));
    }

    /**
     * Tries to find address with given id.
     * Throws special 404 exception when unsuccessful.
     *
     * @param mixed $id The address identifier
     *
     * @return AddressInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findAddressOr404($id)
    {
        if (!$address = $this->container->get('sylius_addressing.manager.address')->findAddress($id)) {
            throw new NotFoundHttpException('Requested address does not exist');
        }

        return $address;
    }

    /**
     * Get engine.
     *
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('sylius_addressing.engine');
    }
}
