<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Provider;

use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @experimental */
final class PathPrefixProvider implements PathPrefixProviderInterface
{
    /** @var UserContextInterface */
    private $userContext;

    /** @var string */
    private $apiRoute;

    public function __construct(UserContextInterface $userContext, string $apiRoute)
    {
        $this->userContext = $userContext;
        $this->apiRoute = $apiRoute;
    }

    public function getPathPrefix(string $path): ?string
    {
        $pathElements = array_values(array_filter(explode('/', $path)));

        if ('/' . $pathElements[0] !== $this->apiRoute) {
            return null;
        }

        if ($pathElements[1] === PathPrefixes::SHOP_PREFIX) {
            return PathPrefixes::SHOP_PREFIX;
        }

        if ($pathElements[1] === PathPrefixes::ADMIN_PREFIX) {
            return PathPrefixes::ADMIN_PREFIX;
        }

        return null;
    }

    public function getCurrentPrefix(): ?string
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();

        if ($user === null || $user instanceof ShopUserInterface) {
            return PathPrefixes::SHOP_PREFIX;
        }

        if ($user instanceof AdminUserInterface) {
            return PathPrefixes::ADMIN_PREFIX;
        }

        return null;
    }
}
