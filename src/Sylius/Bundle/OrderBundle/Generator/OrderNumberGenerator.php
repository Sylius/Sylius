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
use Sylius\Bundle\OrderBundle\Repository\NumberRepositoryInterface;

/**
 * Default order number generator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    /**
     * Number repository.
     *
     * @var NumberRepositoryInterface
     */
    protected $numberRepository;

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
     * @param NumberRepositoryInterface $numberRepository
     * @param integer                   $numberLength
     * @param integer                   $startNumber
     */
    public function __construct(NumberRepositoryInterface $numberRepository, $numberLength = 9, $startNumber = 1)
    {
        $this->numberRepository = $numberRepository;
        $this->numberLength = (int) $numberLength;
        $this->startNumber = (int) $startNumber;
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
        if (null === $number = $this->numberRepository->getLastNumber()) {
            return $this->startNumber;
        }

        return $this->startNumber + $number;
    }
}
