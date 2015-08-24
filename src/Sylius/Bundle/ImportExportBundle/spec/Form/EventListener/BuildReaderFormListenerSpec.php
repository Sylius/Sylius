<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ImportExportBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\ImportExport\Model\ExportProfileInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildReaderFormListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildReaderFormListener');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function let(
        ServiceRegistryInterface $readerRegistry,
        FormFactoryInterface $factory,
        ReaderInterface $reader
    ) {
        $readerRegistry->get('test_reader')->willReturn($reader);
        $readerRegistry->all()->willReturn(array($reader));
        $reader->getType()->willReturn('test_type');
        $this->beConstructedWith($readerRegistry, $factory);
    }

    function it_adds_configuration_fields_in_pre_set_data(
        $factory,
        ExportProfileInterface $exportProfiler,
        FormEvent $event,
        Form $form,
        Form $field
    ) {
        $exportProfiler->getReader()->willReturn('test_reader');
        $exportProfiler->getReaderConfiguration()->willReturn(array());

        $event->getData()->willReturn($exportProfiler);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'readerConfiguration',
            'sylius_test_type_reader',
            Argument::cetera()
            )->willReturn($field);
        $form->add($field)->shouldBeCalled();
        $this->preSetData($event);
    }

    function it_adds_configuration_fields_in_pre_bind(
        $factory,
        FormEvent $event,
        Form $form,
        Form $field
    ) {
        $data = array('reader' => 'test_reader');

        $event->getData()->willReturn($data);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
        'readerConfiguration',
        'sylius_test_type_reader',
        Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();
        $this->preBind($event);
    }

    function it_adds_configuration_fields_in_pre_set_data_without_reader_configured(
        $factory,
        ExportProfileInterface $exportProfiler,
        FormEvent $event,
        Form $form,
        Form $field
    ) {
        $exportProfiler->getReader()->willReturn();
        $exportProfiler->getReaderConfiguration()->willReturn(array());

        $event->getData()->willReturn($exportProfiler);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'readerConfiguration',
            'sylius_test_type_reader',
            Argument::cetera()
            )->willReturn($field);
        $form->add($field)->shouldBeCalled();
        $this->preSetData($event);
    }

    function it_does_not_allow_to_configure_fields_in_pre_set_data_for_class_which_does_not_implement_profile_interface(FormEvent $event)
    {
        $report = '';
        $event->getData()->willReturn($report);
        $this->shouldThrow(new UnexpectedTypeException($report, 'Sylius\Component\ImportExport\Model\ProfileInterface'))
        ->duringPreSetData($event);
    }
}
