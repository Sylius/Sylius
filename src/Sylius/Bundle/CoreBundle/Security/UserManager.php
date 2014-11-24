<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Security;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;

class UserManager extends BaseUserManager
{
    /**
     * {@inheritdoc}
     */
    public function createUser(CustomerInterface $customer = null)
    {
        /** @var $user UserInterface */
        $user = parent::createUser();
        $user->setEnabled(true);
        $user->setCustomer($customer ?: new Customer());

        return $user;
    }
    /**
     * {@inheritdoc}
     */
    public function updateUser(BaseUserInterface $user, $andFlush = true)
    {
        $this->updatePassword($user);
        $this->updateCanonicalFields($user);

        parent::updateUser($user, $andFlush);
    }
}
