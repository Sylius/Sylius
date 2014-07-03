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

use Sylius\Component\Addressing\Model\Address as BaseAddress;

/**
 * Address entity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Address extends BaseAddress implements AddressInterface
{
    /**
     * User.
     *
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
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }
}