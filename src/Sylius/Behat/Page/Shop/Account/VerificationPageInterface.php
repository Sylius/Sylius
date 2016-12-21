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

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface VerificationPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $token
     */
    public function verifyAccount($token);
}
