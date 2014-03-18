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
        $config     = $this->getConfiguration();
        $pluralName = $config->getPluralResourceName();

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setTemplateVar($pluralName)
            ->setData($this->getUser()->getAddresses())
        ;

        return $this->handleView($view);
    }

    /**
     * Set an address as default billing address for the current user.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function setAsDefaultBillingAddressAction(Request $request)
    {
        $address = $this->findOr404($request);

        $user = $this->getUser();
        $user->setBillingAddress($address);

        $manager = $this->getUserManager();
        $manager->persist($user);
        $manager->flush();

        $this->flashHelper->setFlash('success', 'sylius.account.address.flash.billing.success');

        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * Set an address as shipping billing address for the current user.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function setAsDefaultShippingAddressAction(Request $request)
    {
        $address = $this->findOr404($request);

        $user = $this->getUser();
        $user->setShippingAddress($address);

        $manager = $this->getUserManager();
        $manager->persist($user);
        $manager->flush();

        $this->flashHelper->setFlash('success', 'sylius.account.address.flash.shipping.success');

        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * @return ObjectManager
     */
    private function getUserManager()
    {
        return $this->get('sylius.manager.user');
    }

    /**
     * Accesses address or throws 403/404
     *
     * @param Request $request
     * @param array   $criteria
     *
     * @return AddressInterface
     *
     * @throws AccessDeniedException
     */
    public function findOr404(Request $request, array $criteria = array())
    {
        $address = parent::findOr404($request, $criteria);

        if (!$this->getUser()->hasAddress($address)) {
            throw new AccessDeniedException();
        }

        return $address;
    }
}
