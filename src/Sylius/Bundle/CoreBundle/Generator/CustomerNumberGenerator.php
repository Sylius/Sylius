<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Generator;

use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Sylius\Bundle\CoreBundle\Repository\UserRepositoryInterface;

/**
 * Default customer number generator
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CustomerNumberGenerator implements CustomerNumberGeneratorInterface
{
    /**
     * User repository
     *
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * Customer number max length
     *
     * @var int
     */
    protected $numberLength;

    /**
     * Start number
     *
     * @var int
     */
    protected $startNumber;

    /**
     * Constructor
     *
     * @param UserRepositoryInterface $repository
     * @param int $numberLength
     * @param int $startNumber
     */
    public function __construct(UserRepositoryInterface $repository, $numberLength = 9, $startNumber = 1)
    {
        $this->repository = $repository;
        $this->numberLength = $numberLength;
        $this->startNumber = $startNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(UserInterface $user)
    {
        if (null !== $user->getNumber()) {
            return;
        }

        $user->setNumber(str_pad($this->getNextCustomerNumber(), $this->numberLength, 0, STR_PAD_LEFT));
    }

    /**
     * Get next customer number
     *
     * @return int
     */
    protected function getNextCustomerNumber()
    {
        if (!$lastCustomer = $this->repository->findLastCreated()) {
            return $this->startNumber;
        }

        return (int) $lastCustomer->getNumber() + 1;
    }
}
