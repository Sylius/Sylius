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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture\OptionsResolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\ResourceNotFoundException;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\OptionsResolver\Options;

final class LazyOptionTest extends TestCase
{
    #[Test]
    public function it_gets_object_from_provided_repository(): void
    {
        /** @var RepositoryInterface&MockObject $repository */
        $repository = $this->createMock(RepositoryInterface::class);
        $resource = $this->createMock(ResourceInterface::class);
        $options = $this->createMock(Options::class);

        $repository->method('findOneBy')->with(['code' => 'OBJECT_CODE'])->willReturn($resource);

        $closure = LazyOption::getOneBy($repository, 'code');

        self::assertSame($resource, $closure($options, 'OBJECT_CODE'));
    }

    #[Test]
    public function it_finds_an_object_from_provided_repository_or_returns_null(): void
    {
        /** @var RepositoryInterface&MockObject $repository */
        $repository = $this->createMock(RepositoryInterface::class);
        $resource = $this->createMock(ResourceInterface::class);
        $options = $this->createMock(Options::class);

        $repository->method('findOneBy')->willReturnMap([
            [['code' => 'OBJECT_CODE'], $resource],
            [['code' => 'NOT_EXISTING_OBJECT_CODE'], null],
        ]);

        $closure = LazyOption::findOneBy($repository, 'code');

        self::assertSame($resource, $closure($options, 'OBJECT_CODE'));
        self::assertNull($closure($options, 'NOT_EXISTING_OBJECT_CODE'));
    }

    #[Test]
    public function it_returns_previous_value_if_it_is_an_object_null_or_empty_array(): void
    {
        /** @var RepositoryInterface&MockObject $repository */
        $repository = $this->createMock(RepositoryInterface::class);
        $resource = $this->createMock(ResourceInterface::class);
        $options = $this->createMock(Options::class);

        $repository->expects($this->never())->method('findOneBy');

        $getOneByClosure = LazyOption::getOneBy($repository, 'code');

        self::assertSame($resource, $getOneByClosure($options, $resource));
        self::assertNull($getOneByClosure($options, []));
        self::assertNull($getOneByClosure($options, null));

        $findOneByClosure = LazyOption::findOneBy($repository, 'code');

        self::assertSame($resource, $findOneByClosure($options, $resource));
        self::assertNull($findOneByClosure($options, []));
        self::assertNull($findOneByClosure($options, null));
    }

    #[Test]
    public function it_throws_an_exception_if_object_cannot_be_found(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        /** @var RepositoryInterface&MockObject $repository */
        $repository = $this->createMock(RepositoryInterface::class);
        $options = $this->createMock(Options::class);

        $repository->method('findOneBy')->with(['code' => 'OBJECT_CODE'])->willReturn(null);
        $repository->method('getClassName')->willReturn('App\\Entity');

        $closure = LazyOption::getOneBy($repository, 'code');

        $closure($options, 'OBJECT_CODE');
    }
}
