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

namespace spec\Sylius\Bundle\AdminBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminUriBasedSectionResolverSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/admin');
    }

    function it_is_uri_based_section_resolver(): void
    {
        $this->shouldImplement(UriBasedSectionResolverInterface::class);
    }

    function it_returns_admin_if_path_starts_with_slash_admin(): void
    {
        $this->getSection('/admin/something')->shouldBeLike(new AdminSection());
        $this->getSection('/admin')->shouldBeLike(new AdminSection());
    }

    function it_throws_exception_if_path_does_not_start_with_slash_admin(): void
    {
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/admi']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/shop']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/api/asd']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/en_US/admin']);
    }
}
