<?php

namespace Sylius\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MainController
 * Main actions.
 */
class MainController extends Controller
{
    /**
     * Dashboard page.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainAction(Request $request)
    {
        $orderRepository = $this->get('sylius.repository.order');
        $customerRepository = $this->get('sylius.repository.customer');
        $productRepository = $this->get('sylius.repository.product');

        return $this->render('SyliusBackendBundle:Main:index.html.twig', array(
            'orders_count' => $orderRepository->countBetweenDates(new \DateTime('100 year ago'), new \DateTime()),
            'products_count' => $productRepository->countProducts(),
            'orders' => $orderRepository->findBy(array(), array('updatedAt' => 'desc'), 5),
            'customers' => $customerRepository->findBy(array(), array('id' => 'desc'), 5),
            'sales' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
        ));
    }


}
