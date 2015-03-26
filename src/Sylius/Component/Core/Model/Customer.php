<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\Customer as BaseCustomer;

class Customer extends BaseCustomer implements CustomerInterface
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function isRegistered()
    {
        return null !== $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        if (null === $this->user) {
            parent::setFirstName($firstName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        if (null === $this->user) {
            parent::setLastName($lastName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        if (null === $this->user) {
            parent::setEmail($email);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setGender($gender)
    {
        if (null === $this->user) {
            parent::setGender($gender);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        if (null === $this->user) {
            parent::setCurrency($currency);
        }
    }
}
