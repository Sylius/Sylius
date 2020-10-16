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

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/** @experimental */
final class RequestApiPathPrefixProvider implements RequestApiPathPrefixProviderInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var ApiPathPrefixProviderInterface */
    private $apiPathPrefixProvider;

    public function __construct(
        RequestStack $requestStack,
        ApiPathPrefixProviderInterface $apiPathPrefixProvider
    ) {
        $this->requestStack = $requestStack;
        $this->apiPathPrefixProvider = $apiPathPrefixProvider;
    }

    public function getCurrentRequestPrefix(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return null;
        }

        return $this->apiPathPrefixProvider->getPathPrefix($request->getPathInfo());
    }
}
