<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Context;

use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerContext implements CustomerContextInterface
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Gets customer based on currently logged user.
     *
     * @return CustomerInterface|null
     */
    public function getCustomer()
    {
        if ($this->securityContext->getToken() && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')
            && $this->securityContext->getToken()->getUser()
        ) {
            return $this->securityContext->getToken()->getUser()->getCustomer();
        }

        return null;
    }
}
