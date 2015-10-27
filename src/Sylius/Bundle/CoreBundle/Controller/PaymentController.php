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

use Sylius\Bundle\PaymentBundle\Controller\PaymentController as BasePaymentController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Payment controller.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PaymentController extends BasePaymentController
{

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->isGrantedOr403('index');

        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();


        $user = $this->getUser();
        if ($user) {
            if ($roles = $user->getAuthorizationRoles()) {
                if (array_key_exists(0, $roles)) {
                    if ($roles[0]->getCode() == 'store_owner') {
                        $storeRepository = $this->container->get('sylius.repository.store');
                        $store = $storeRepository->findOneBy(array('user' => $user->getId()));
                        $criteria = array('store' => $store->getId());
                    }
                }
            }
        }


        $resources = $repository->findBy($criteria);

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources);

        return $this->handleView($view);


    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $payment = $this->findOr404($request);

        $logRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData(array(
                $this->config->getResourceName() => $payment,
                'logs' => $logRepository->getLogEntries($payment)
            ));

        return $this->handleView($view);
    }
}
