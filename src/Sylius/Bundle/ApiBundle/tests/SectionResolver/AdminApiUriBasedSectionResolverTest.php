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

namespace Tests\Sylius\Bundle\ApiBundle\SectionResolver;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiUriBasedSectionResolver;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class AdminApiUriBasedSectionResolverTest extends TestCase
{
    private AdminApiUriBasedSectionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new AdminApiUriBasedSectionResolver('/api/v2/admin');
    }

    public function testUriBasedSectionResolver(): void
    {
        self::assertInstanceOf(UriBasedSectionResolverInterface::class, $this->resolver);
    }

    public function testReturnsAdminApiSectionIfPathStartsWithApiV2Admin(): void
    {
        $this->assertEquals(new AdminApiSection(), $this->resolver->getSection('/api/v2/admin/something'));

        $this->assertEquals(new AdminApiSection(), $this->resolver->getSection('/api/v2/admin'));
    }

    /**
     * @dataProvider nonMatchingPathsProvider
     */
    public function testThrowsAnExceptionIfPathDoesNotStartWithApiV2Admin(string $path): void
    {
        self::expectException(SectionCannotBeResolvedException::class);

        $this->resolver->getSection($path);
    }

    public static function nonMatchingPathsProvider(): array
    {
        return [
            ['/shop'],
            ['/admin'],
            ['/en_US/api'],
            ['/api/v1'],
            ['/api/v2'],
        ];
    }
}
