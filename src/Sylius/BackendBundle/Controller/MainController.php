<?php

namespace Sylius\BackendBundle\Controller;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MainController
 * Main actions
 * @package Sylius\BackendBundle\Controller
 */
class MainController extends Controller
{

    /**
     * Dashboard page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainAction(Request $request)
    {

        $orderRepository = $this->get('sylius.repository.order');
        $customerRepository = $this->get('sylius.repository.customer');
        $productRepository = $this->get('sylius.repository.product');
        $userRepository = $this->get('sylius.repository.user');

//        exit(dump($productRepository->countProducts()));

        return $this->render('SyliusBackendBundle:Main:index.html.twig', array(
            'orders_count' => $orderRepository->countBetweenDates(new \DateTime('100 year ago'), new \DateTime()),
            'products_count' => $productRepository->countProducts(),
            'orders' => $orderRepository->findBy(array(), array('updatedAt' => 'desc'), 5),
            'customers' => $customerRepository->findBy(array(), array('id' => 'desc'), 5),
            'registrations_count' => $userRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales_confirmed' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'),
                new \DateTime(), OrderInterface::STATE_CONFIRMED),
            'sales_cancelled' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'),
                new \DateTime(), OrderInterface::STATE_CANCELLED),
            'sales_pending' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'),
                new \DateTime(), OrderInterface::STATE_PENDING),
        ));
    }
}
