<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Generator;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\SalesBundle\Repository\OrderRepositoryInterface;

/**
 * Default order number generator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    /**
     * Order repository.
     *
     * @var OrderRepositoryInterface
     */
    protected $repository;

    /**
     * Order number max length.
     *
     * @var integer
     */
    protected $numberLength;

    /**
     * Constructor.
     *
     * @param OrderRepositoryInterface $repository
     * @param integer                  $numberLength
     */
    public function __construct(OrderRepositoryInterface $repository, $numberLength = 6)
    {
        $this->repository = $repository;
        $this->numberLength = $numberLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order)
    {
        if (null === $order->getNumber()) {
            $order->setNumber(str_pad((int) $this->getLastOrderNumber() + 1, $this->numberLength, 0, STR_PAD_LEFT));
        }
    }

    /**
     * Get last order number.
     *
     * @return string
     */
    protected function getLastOrderNumber()
    {
        $lastOrders = $this->repository->findRecentOrders(1);

        if (empty($lastOrders)) {
            return str_repeat('0', $this->numberLength);
        }

        return current($lastOrders)->getNumber();
    }
}
