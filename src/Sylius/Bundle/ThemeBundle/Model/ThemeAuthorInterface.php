<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Model;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ThemeAuthorInterface
{
    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string|null
     */
    public function getHomepage();

    /**
     * @param string $homepage
     */
    public function setHomepage($homepage);

    /**
     * @return string|null
     */
    public function getRole();

    /**
     * @param string $role
     */
    public function setRole($role);
}
