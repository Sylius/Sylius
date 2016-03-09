<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\User;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
interface RegisterPageInterface extends PageInterface
{
    /**
     * @param string $email
     *
     * @throws ElementNotFoundException
     */
    public function register($email);

    /**
     * @return bool
     */
    public function wasRegistrationSuccessful();
}
