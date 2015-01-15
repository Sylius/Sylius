<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Component\Core\Model\UserInterface;

/**
 * Customer welcome mailer implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CustomerWelcomeMailer extends AbstractMailer implements CustomerWelcomeMailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendCustomerWelcome(UserInterface $user)
    {
        $this->sendEmail(array('user' => $user), $user->getEmail());
    }
}
