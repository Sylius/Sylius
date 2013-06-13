<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Entity\User;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * User repository
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * Generates a unique customer ID which is a 10 length ID
     * made up of first name, last name and random number.
     *
     * @param User $user
     * @return string
     */
    public function generateCustomerId(User $user)
    {
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $customerId = $user->getCustomerId();

        if (empty($customerId) && !empty($firstName) && !empty($lastName))
        {
            do {
                $customerId = substr(strtoupper($lastName), 0, 4) . substr(strtoupper($firstName), 0, 2);
                $customerId .= substr(rand(100000, 999999), 0, 10 - strlen($customerId));
            }
            while (null !== $this->findOneByCustomerId($customerId));
        }

        $user->setCustomerId($customerId);
        $this->getEntityManager()->flush($user);
    }
}