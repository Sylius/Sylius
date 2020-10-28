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

namespace spec\Sylius\Component\Addressing\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProvinceProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_provides_province_by_name(
        RepositoryInterface $repository,
        ProvinceInterface $province
    ): void {
        $repository->findBy(['name' => 'Queensland'])->willReturn($province);

        $this->findByName('Queensland')->shouldReturn($province);
    }
}
