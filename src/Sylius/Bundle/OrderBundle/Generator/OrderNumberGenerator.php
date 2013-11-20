<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Generator;

use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Repository\OrderRepositoryInterface;

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
     * Start number
     *
     * @var integer
     */
    protected $startNumber;

    /**
     * Constructor.
     *
     * @param OrderRepositoryInterface $repository
     * @param integer                  $numberLength
     * @param integer                  $startNumber
     */
    public function __construct(OrderRepositoryInterface $repository, $numberLength = 9, $startNumber = 1)
    {
        $this->repository = $repository;
        $this->numberLength = $numberLength;
        $this->startNumber = $startNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order)
    {
        if (null !== $order->getNumber()) {
            return;
        }

        $order->setNumber(str_pad($this->getNextOrderNumber(), $this->numberLength, 0, STR_PAD_LEFT));
    }

    /**
     * Get next order number.
     *
     * @return string
     */
    protected function getNextOrderNumber()
    {
        $lastOrders = $this->repository->findRecentOrders(1);

        if (empty($lastOrders)) {
            return $this->startNumber;
        }

        return (int) current($lastOrders)->getNumber() + 1;
    }
}
