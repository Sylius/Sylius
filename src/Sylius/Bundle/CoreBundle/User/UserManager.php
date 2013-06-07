<?php
/**
 * Author: jj
 * Date: 6/7/13
 */

namespace Sylius\Bundle\CoreBundle\User;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface;

class UserManager extends BaseUserManager
{
    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);
        $this->updateCustomerId($user);

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * Updates the customer ID : 10 length ID made up of first name, last name and random number.
     *
     * @param UserInterface $user
     */
    public function updateCustomerId(UserInterface $user)
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
            while (null !== $this->findUserByCustomerId($customerId));

            $user->setCustomerId($customerId);
        }
    }

    public function findUserByCustomerId($customerId)
    {
        return $this->findUserBy(array('customerId' => $customerId));
    }
}