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

namespace Sylius\Behat\Service\Provider;

use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Provider\ApiPathPrefixProvider;
use Sylius\Bundle\ApiBundle\Provider\ApiPathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Provider\RequestApiPathPrefixProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

/** @experimental */
final class RequestApiPathPrefixProvider implements RequestApiPathPrefixProviderInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var ApiPathPrefixProviderInterface */
    private $apiPathPrefixProvider;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(
        RequestStack $requestStack,
        ApiPathPrefixProviderInterface $apiPathPrefixProvider,
        UserContextInterface $userContext
    ) {
        $this->requestStack = $requestStack;
        $this->apiPathPrefixProvider = $apiPathPrefixProvider;
        $this->userContext = $userContext;
    }

    public function getCurrentRequestPrefix(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            /** @var UserInterface|null $user */
            $user = $this->userContext->getUser();

            if ($user === null || $user instanceof ShopUserInterface) {
                return ApiPathPrefixProvider::SHOP_PREFIX;
            }

            if ($user instanceof AdminUserInterface) {
                return ApiPathPrefixProvider::ADMIN_PREFIX;
            }

            return null;
        }

        $path = $request->getPathInfo();

        return $this->apiPathPrefixProvider->getPathPrefix($path);
    }
}
