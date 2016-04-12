<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\User;

use Sylius\Behat\Page\PageInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
interface RegisterPageInterface extends PageInterface
{
    /**
     * @param string $email
     */
    public function register($email);

    /**
     * @return bool
     */
    public function wasRegistrationSuccessful();
}
