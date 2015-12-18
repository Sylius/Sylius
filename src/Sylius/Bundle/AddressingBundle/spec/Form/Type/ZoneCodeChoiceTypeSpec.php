<?php

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneCodeChoiceTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\ZoneCodeChoiceType');
    }

    function it_extends_country_choice_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_zone_code_choice');
    }

    function it_configures_options(OptionsResolver $resolver, ZoneInterface $zone, RepositoryInterface $repository)
    {
        $zone->getCode()->willReturn('EU');
        $zone->getName()->willReturn('European Union');

        $repository->findAll()->willReturn(array($zone));

        $resolver
            ->setDefaults(array(
                'choices'     => array('EU' => 'European Union'),
                'label'       => 'sylius.form.zone.types.zone',
                'empty_value' => 'sylius.form.zone.select',
            ))
            ->willReturn($resolver)
        ;

        $this->configureOptions($resolver);
    }
}
