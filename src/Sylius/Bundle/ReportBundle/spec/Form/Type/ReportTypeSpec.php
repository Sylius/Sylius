<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Prophecy\Argument;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;
use Sylius\Component\Report\Renderer\RendererInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReportTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $rendererRegistry, ServiceRegistryInterface $dataFetcherRegistry)
    {
        $this->beConstructedWith('Sylius\Component\Report\Model\Report', array('sylius'), $rendererRegistry, $dataFetcherRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\Type\ReportType');
    }

    function it_should_be_abstract_resource_type_object()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_build_form_with_proper_fields(
        FormBuilderInterface $builder,
        FormFactoryInterface $factory,
        $dataFetcherRegistry,
        $rendererRegistry,
        RendererInterface $renderer,
        DataFetcherInterface $dataFetcher
    ) {
        $builder->getFormFactory()->willReturn($factory);

        $builder->add('name', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('code', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('description', 'textarea', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('renderer', 'sylius_renderer_choice', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('dataFetcher', 'sylius_data_fetcher_choice', Argument::any())->shouldBeCalled()->willReturn($builder);

        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportRendererFormListener'))->shouldBeCalled()->willReturn($builder);
        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportDataFetcherFormListener'))->shouldBeCalled()->willReturn($builder);

        $renderer->getType()->willReturn('test_renderer');
        $rendererRegistry->all()->willReturn(array('test_renderer' => $renderer));
        $builder->create('rendererConfiguration', 'sylius_renderer_test_renderer')->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn(Argument::type('Symfony\Component\Form\Form'));

        $dataFetcher->getType()->willReturn('test_data_fetcher');
        $dataFetcherRegistry->all()->willReturn(array('test_data_fetcher' => $dataFetcher));
        $builder->create('dataFetcherConfiguration', 'sylius_data_fetcher_test_data_fetcher')->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn(Argument::type('Symfony\Component\Form\Form'));

        $prototypes = array(
            'renderers' => array(
                'test_renderer' => Argument::type('Symfony\Component\Form\Form'),
                ),
            'dataFetchers' => array(
                'test_data_fetcher' => Argument::type('Symfony\Component\Form\Form'),
                ),
            );
        $builder->setAttribute('prototypes', $prototypes)->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_builds_view(
        FormConfigInterface $config,
        FormView $view,
        FormInterface $form,
        FormInterface $formTable,
        FormInterface $formUserRegistration
    ) {
        $prototypes = array(
            'dataFetchers' => array('user_registration' => $formUserRegistration),
            'renderers' => array('table' => $formTable),
        );
        $config->getAttribute('prototypes')->willReturn($prototypes);
        $form->getConfig()->willReturn($config);

        $formTable->createView($view)->shouldBeCalled();
        $formUserRegistration->createView($view)->shouldBeCalled();

        $this->buildView($view, $form, array());
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_report');
    }
}
