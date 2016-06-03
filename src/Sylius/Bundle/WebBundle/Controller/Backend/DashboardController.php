<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Backend;

use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Backend dashboard controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardController extends Controller
{
    /**
     * Backend dashboard display action.
     */
    public function mainAction()
    {
        $orderRepository = $this->get('sylius.repository.order');
        $customerRepository = $this->get('sylius.repository.customer');
        $userRepository = $this->get('sylius.repository.user');

        return $this->render('SyliusWebBundle:Backend/Dashboard:main.html.twig', [
            'orders_count' => $orderRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'orders' => $orderRepository->findCompleted(['updatedAt' => 'desc'], 5),
            'customers' => $customerRepository->findBy([], ['id' => 'desc'], 5),
            'registrations_count' => $userRepository->countBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime()),
            'sales_confirmed' => $orderRepository->revenueBetweenDates(new \DateTime('1 month ago'), new \DateTime(), OrderInterface::STATE_CONFIRMED),
        ]);
    }
}
