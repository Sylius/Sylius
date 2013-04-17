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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;

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
     * @var ObjectRepository
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
     * @param ObjectRepository $repository
     * @param integer          $numberLength
     */
    public function __construct(ObjectRepository $repository, $numberLength = 6)
    {
        $this->repository = $repository;
        $this->numberLength = $numberLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order)
    {
        $order->setNumber(str_pad((int) $this->getLastOrderNumber() + 1, $this->numberLength, 0, STR_PAD_LEFT));
    }

    /**
     * Get last order number.
     *
     * @return string
     */
    protected function getLastOrderNumber()
    {
        $lastOrder = current($this->repository->findBy(array(), array('createdAt' => 'desc'), 1));

        if (!$lastOrder) {
            return str_repeat('0', $this->numberLength);
        }

        return $lastOrder->getNumber();
    }
}
