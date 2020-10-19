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

namespace spec\Sylius\Bundle\AdminApiBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminApiUriBasedSectionResolverSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/api');
    }

    function it_it_uri_based_section_resolver(): void
    {
        $this->shouldImplement(UriBasedSectionResolverInterface::class);
    }

    function it_returns_admin_api_section_if_path_starts_with_slash_api(): void
    {
        $this->getSection('/api/something')->shouldBeLike(new AdminApiSection());
        $this->getSection('/api')->shouldBeLike(new AdminApiSection());
    }

    function it_throws_exception_if_path_does_not_start_with_slash_api(): void
    {
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/ap']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/shop']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/admin/asd']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/en_US/api']);
    }
}
