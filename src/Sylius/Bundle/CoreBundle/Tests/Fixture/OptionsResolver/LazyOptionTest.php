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

namespace Sylius\Bundle\CoreBundle\Tests\Fixture\OptionsResolver;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\ResourceNotFoundException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;

final class LazyOptionTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_object_from_provided_repository(): void
    {
        /** @var RepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);
        $options = $this->prophesize(Options::class);

        $repository->findOneBy(['code' => 'OBJECT_CODE'])->willReturn($resource->reveal());

        $closure = LazyOption::getOneBy($repository->reveal(), 'code');

        self::assertSame($resource->reveal(), $closure($options->reveal(), 'OBJECT_CODE'));
    }

    /**
     * @test
     */
    public function it_finds_an_object_from_provided_repository_or_returns_null(): void
    {
        /** @var RepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);
        $options = $this->prophesize(Options::class);

        $repository->findOneBy(['code' => 'OBJECT_CODE'])->willReturn($resource->reveal());
        $repository->findOneBy(['code' => 'NOT_EXISTING_OBJECT_CODE'])->willReturn(null);

        $closure = LazyOption::findOneBy($repository->reveal(), 'code');

        self::assertSame($resource->reveal(), $closure($options->reveal(), 'OBJECT_CODE'));
        self::assertNull($closure($options->reveal(), 'NOT_EXISTING_OBJECT_CODE'));
    }

    /**
     * @test
     */
    public function it_returns_previous_value_if_it_is_an_object_null_or_empty_array(): void
    {
        /** @var RepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(RepositoryInterface::class);
        $resource = $this->prophesize(ResourceInterface::class);
        $options = $this->prophesize(Options::class);

        $repository->findOneBy(Argument::any())->shouldNotBeCalled();

        $getOneByClosure = LazyOption::getOneBy($repository->reveal(), 'code');

        self::assertSame($resource->reveal(), $getOneByClosure($options->reveal(), $resource->reveal()));
        self::assertNull($getOneByClosure($options->reveal(), []));
        self::assertNull($getOneByClosure($options->reveal(), null));

        $findOneByClosure = LazyOption::findOneBy($repository->reveal(), 'code');

        self::assertSame($resource->reveal(), $findOneByClosure($options->reveal(), $resource->reveal()));
        self::assertNull($findOneByClosure($options->reveal(), []));
        self::assertNull($findOneByClosure($options->reveal(), null));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_object_cannot_be_found(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        /** @var RepositoryInterface|ObjectProphecy $repository */
        $repository = $this->prophesize(RepositoryInterface::class);
        $options = $this->prophesize(Options::class);

        $repository->findOneBy(['code' => 'OBJECT_CODE'])->willReturn(null);
        $repository->getClassName()->willReturn('App\\Entity');

        $closure = LazyOption::getOneBy($repository->reveal(), 'code');

        $closure($options->reveal(), 'OBJECT_CODE');
    }
}
