<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends ResourceController
{
    /**
     * Render order filter form.
     */
    public function filterFormAction(Request $request)
    {
        $form = $this->getFormFactory()->createNamed('criteria', 'sylius_order_filter');

        return $this->renderResponse('SyliusWebBundle:Backend/Order:filterForm.html', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function indexByUserAction(Request $request, $id)
    {
        $config = $this->getConfiguration();
        $sorting = $config->getSorting();

        $user = $this
            ->getUserController()
            ->findOr404(array('id' => $id));

        $paginator = $this
            ->getRepository()
            ->createByUserPaginator($user, $sorting);

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($config->getPaginationMaxPerPage());

        return $this->renderResponse('SyliusWebBundle:Backend/Order:indexByUser.html', array(
            'user' => $user,
            'orders' => $paginator
        ));
    }

    private function getFormFactory()
    {
        return $this->get('form.factory');
    }

    /**
     * Get user controller.
     *
     * @return ResourceController
     */
    private function getUserController()
    {
        return $this->get('sylius.controller.user');
    }
}
