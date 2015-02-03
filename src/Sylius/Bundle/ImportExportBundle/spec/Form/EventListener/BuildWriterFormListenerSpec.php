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
use Sylius\Component\ImportExport\Writer\WriterInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class BuildWriterFormListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildWriterFormListener');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function let(
        ServiceRegistryInterface $writerRegistry, 
        FormFactoryInterface $factory, 
        WriterInterface $writer)
    {
        $writerRegistry->get('test_writer')->willReturn($writer);
        $writer->getType()->willReturn('test_type');
        $this->beConstructedWith($writerRegistry, $factory);
    }

    function it_adds_configuration_fields_in_pre_set_data(
        $factory,
        ExportProfileInterface $exportProfiler,
        FormEvent $event,
        Form $form,
        Form $field)
    {
        $exportProfiler->getWriter()->willReturn('test_writer');
        $exportProfiler->getWriterConfiguration()->willReturn(array());

        $event->getData()->willReturn($exportProfiler);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'writerConfiguration',
            'sylius_test_type_writer',
            Argument::cetera()
            )->willReturn($field);
        $form->add($field)->shouldBeCalled();
        $this->preSetData($event);
    }

    function it_adds_configuration_fields_in_pre_bind(
        $factory,
        FormEvent $event,
        Form $form,
        Form $field)
    {
    $data = array('writer' => 'test_writer');

    $event->getData()->willReturn($data);
    $event->getForm()->willReturn($form);

    $factory->createNamed(
        'writerConfiguration',
        'sylius_test_type_writer',
        Argument::cetera()
        )->willReturn($field);

    $form->add($field)->shouldBeCalled();
    $this->preBind($event);

    }

    function it_does_not_allow_to_confidure_fields_in_pre_set_data_for_other_class_then_export_profiler(FormEvent $event)
    {
        $report = '';
        $event->getData()->willReturn($report);
        $this->shouldThrow(new UnexpectedTypeException($report, 'Sylius\Component\ImportExport\Model\ExportProfileInterface'))
        ->duringPreSetData($event);
    }
}