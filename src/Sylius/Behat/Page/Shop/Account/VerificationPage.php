<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class VerificationPage extends SymfonyPage implements VerificationPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function verifyAccount($token)
    {
        $this->tryToOpen(['token' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_user_verification';
    }
}
