<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\Renderer\RendererInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class BuildReportRendererFormSubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportRendererFormSubscriber');
    }

    public function it_implements_data_fetcher_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function let(ServiceRegistryInterface $rendererRegistry, FormFactoryInterface $factory, RendererInterface $renderer)
    {
        $rendererRegistry->get('test_renderer')->willReturn($renderer);
        $renderer->getType()->willReturn('test_type');

        $this->beConstructedWith($rendererRegistry, $factory);
    }

    public function it_adds_configuration_fields_in_pre_set_data(
        $factory,
        ReportInterface $report,
        FormEvent $event,
        Form $form,
        Form $field)
    {
        $report->getRenderer()->willReturn('test_renderer');
        $report->getRendererConfiguration()->willReturn(array());

        $event->getData()->willReturn($report);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'rendererConfiguration',
            'sylius_renderer_test_type',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    public function it_adds_configuration_fields_in_pre_bind(
        $factory,
        FormEvent $event,
        Form $form,
        Form $field)
    {
        $data = array('renderer' => 'test_renderer');

        $event->getData()->willReturn($data);
        $event->getForm()->willReturn($form);

        $factory->createNamed(
            'rendererConfiguration',
            'sylius_renderer_test_type',
            Argument::cetera()
        )->willReturn($field);

        $form->add($field)->shouldBeCalled();

        $this->preBind($event);
    }

    public function it_does_not_allow_to_confidure_fields_in_pre_set_data_for_other_class_then_report(FormEvent $event)
    {
        $report = '';
        $event->getData()->willReturn($report);
        $this->shouldThrow(new UnexpectedTypeException($report, 'Sylius\Component\Report\Model\ReportInterface'))
            ->duringPreSetData($event);
    }
}
