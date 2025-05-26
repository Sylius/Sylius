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

namespace Tests\Sylius\Bundle\AdminBundle\SectionResolver;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminUriBasedSectionResolver;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminUriBasedSectionResolverTest extends TestCase
{
    private AdminUriBasedSectionResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new AdminUriBasedSectionResolver('/admin');
    }

    public function testUriBasedSectionResolver(): void
    {
        $this->assertInstanceOf(UriBasedSectionResolverInterface::class, $this->resolver);
    }

    public function testReturnsAdminIfPathStartsWithSlashAdmin(): void
    {
        $this->assertEquals(new AdminSection(), $this->resolver->getSection('/admin'));
        $this->assertEquals(new AdminSection(), $this->resolver->getSection('/admin/something'));
    }

    #[TestWith(['/admi'])]
    #[TestWith(['/shop'])]
    #[TestWith(['/api/asd'])]
    #[TestWith(['/en_US/admin'])]
    public function testThrowsExceptionIfPathDoesNotStartWithSlashAdmin(string $path): void
    {
        $this->expectException(SectionCannotBeResolvedException::class);

        $this->resolver->getSection($path);
    }
}
