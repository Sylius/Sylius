<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ZoneToIdentifierTransformerSpec extends ObjectBehavior
{

    function let(ObjectRepository $zoneRepository)
    {
        $this->beConstructedWith($zoneRepository, 'name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\DataTransformer\ZoneToIdentifierTransformer');
    }

    function it_returns_empty_string_if_null_transormed()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_should_complain_if_not_Sylius_zone_transformed()
    {
        $zone = new \stdClass();

        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform($zone)
        ;
    }

    function it_should_transform_zone_into_its_identifier_value(ZoneInterface $zone)
    {
        $zone->getName()->willReturn('EU');

        $this->transform($zone)->shouldReturn('EU');
    }

    function it_returns_null_if_empty_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_returns_null_if_zone_not_found_on_reverse_transform($zoneRepository)
    {
        $zoneRepository
            ->findOneBy(array('name' => 'EU'))
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->reverseTransform('EU')->shouldReturn(null);
    }

    function it_should_zone_if_found_on_reverse_transform(ZoneInterface $zoneRepository, $zone)
    {
        $zoneRepository
            ->findOneBy(array('name' => 'EU'))
            ->shouldBeCalled()
            ->willReturn($zone)
        ;

        $this->reverseTransform('EU')->shouldReturn($zone);
    }
}
