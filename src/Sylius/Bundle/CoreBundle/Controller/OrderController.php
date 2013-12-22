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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends ResourceController
{
    /**
     * Render order filter form.
     */
    public function filterFormAction(Request $request)
    {
        $form = $this->getFormFactory()->createNamed('criteria', 'sylius_order_filter', $request->query->get('criteria'));

        return $this->render('SyliusWebBundle:Backend/Order:filterForm.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexByUserAction(Request $request, $id)
    {
        $config = $this->getConfiguration();
        $sorting = $config->getSorting();

        $user = $this->get('sylius.repository.user')
            ->findOneById($id);

        if (!isset($user)) {
            throw new NotFoundHttpException('Requested user does not exist');
        }

        $paginator = $this
            ->getRepository()
            ->createByUserPaginator($user, $sorting);

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($config->getPaginationMaxPerPage());

        return $this->render('SyliusWebBundle:Backend/Order:indexByUser.html.twig', array(
            'user' => $user,
            'orders' => $paginator
        ));
    }

    private function getFormFactory()
    {
        return $this->get('form.factory');
    }
}
