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

namespace spec\Sylius\Bundle\ApiBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminApiUriBasedSectionResolverSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/api/v2/admin');
    }

    function it_is_uri_based_section_resolver(): void
    {
        $this->shouldImplement(UriBasedSectionResolverInterface::class);
    }

    function it_returns_admin_api_section_if_path_starts_with_api_v2_admin(): void
    {
        $this->getSection('/api/v2/admin/something')->shouldBeLike(new AdminApiSection());
        $this->getSection('/api/v2/admin')->shouldBeLike(new AdminApiSection());
    }

    function it_throws_an_exception_if_path_does_not_start_with_api_v2_admin(): void
    {
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/shop']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/admin']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/en_US/api']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/api/v1']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/api/v2']);
    }
}
