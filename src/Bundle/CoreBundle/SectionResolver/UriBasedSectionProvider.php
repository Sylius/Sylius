<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\SectionResolver;

use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class UriBasedSectionProvider implements SectionProviderInterface
{
    /** @var iterable|UriBasedSectionResolverInterface[] */
    private iterable $resolvers;

    public function __construct(private RequestStack $requestStack, iterable $resolvers)
    {
        Assert::allIsInstanceOf($resolvers, UriBasedSectionResolverInterface::class);
        $this->resolvers = $resolvers;
    }

    public function getSection(): ?SectionInterface
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return null;
        }

        $uri = $request->getPathInfo();

        foreach ($this->resolvers as $resolver) {
            try {
                return $resolver->getSection($uri);
            } catch (SectionCannotBeResolvedException) {
            }
        }

        return null;
    }
}
