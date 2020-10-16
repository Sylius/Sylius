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

namespace Sylius\Behat\Service\Checker;

use Sylius\Bundle\ApiBundle\Provider\ApiPathPrefixProviderInterface;
use Webmozart\Assert\Assert;

final class ApiPrefixChecker implements ApiPrefixCheckerInterface
{
    /** @var ApiPathPrefixProviderInterface */
    private $apiPathPrefixProvider;

    public function __construct(ApiPathPrefixProviderInterface $apiPathPrefixProvider)
    {
        $this->apiPathPrefixProvider = $apiPathPrefixProvider;
    }

    public function searchProperPrefixesRecursively(array $content, string $prefixType): void
    {
        foreach ($content as $element) {
            if (is_array($element)) {
                $this->searchProperPrefixesRecursively($element, $prefixType);

                continue;
            }

            $prefix = $this->apiPathPrefixProvider->getPathPrefix((string) $element);

            if ($prefix !== null) {
                Assert::same($prefix, $prefixType);
            }
        }
    }
}
