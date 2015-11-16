<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Reloader;

use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReloader implements UserReloaderInterface
{
    /**
     * @var ResourceManagerInterface
     */
    private $userManager;

    /**
     * @param ResourceManagerInterface $userManager
     */
    public function __construct(ResourceManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritDoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $this->userManager->refresh($user);
    }
}
