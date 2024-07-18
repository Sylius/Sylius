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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Country;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Exception\ProvinceCannotBeRemoved;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $persistProcessor,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
    ): void {
        $this->beConstructedWith($persistProcessor, $countryProvincesDeletionChecker);
    }

    function it_throws_an_exception_if_object_is_not_a_country(
        ProcessorInterface $persistProcessor,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
        HttpOperation $operation,
    ): void {
        $countryProvincesDeletionChecker->isDeletable(Argument::any())->shouldNotBeCalled();

        $persistProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), $operation, [], []])
        ;
    }

    function it_uses_decorated_data_persister_to_persist_country(
        ProcessorInterface $persistProcessor,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
        CountryInterface $country,
    ): void {
        $operation = new Post();
        $uriVariables = [];
        $context = [];

        $countryProvincesDeletionChecker->isDeletable($country)->willReturn(true);

        $persistProcessor->process($country, $operation, $uriVariables, $context)->willReturn($country);

        $this->process($country, $operation, $uriVariables, $context)->shouldReturn($country);
    }

    function it_throws_an_error_if_the_province_within_a_country_is_in_use(
        ProcessorInterface $persistProcessor,
        CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
        CountryInterface $country,
        HttpOperation $operation,
    ): void {
        $uriVariables = [];
        $context = [];

        $countryProvincesDeletionChecker->isDeletable($country)->willReturn(false);

        $persistProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(ProvinceCannotBeRemoved::class)
            ->during('process', [$country, $operation, $uriVariables, $context])
        ;
    }
}
