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
use Prophecy\Argument;
use Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportDataFetcherFormSubscriber;
use Sylius\Bundle\ReportBundle\Form\EventListener\BuildReportRendererFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;
use Sylius\Component\Report\Model\Report;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReportTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $rendererRegistry, ServiceRegistryInterface $dataFetcherRegistry)
    {
        $this->beConstructedWith(Report::class, ['sylius'], $rendererRegistry, $dataFetcherRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\Type\ReportType');
    }

    function it_should_be_abstract_resource_type_object()
    {
        $this->shouldHaveType(AbstractResourceType::class);
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
        $builder->add('description', 'textarea', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('renderer', 'sylius_renderer_choice', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('dataFetcher', 'sylius_data_fetcher_choice', Argument::any())->shouldBeCalled()->willReturn($builder);

        $builder->addEventSubscriber(Argument::type(BuildReportRendererFormSubscriber::class))->shouldBeCalled()->willReturn($builder);
        $builder->addEventSubscriber(Argument::type(BuildReportDataFetcherFormSubscriber::class))->shouldBeCalled()->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::type(AddCodeFormSubscriber::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $renderer->getType()->willReturn('sylius_renderer_test_renderer');
        $rendererRegistry->all()->willReturn(['test_renderer' => $renderer]);
        $builder->create('rendererConfiguration', 'sylius_renderer_test_renderer')->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn(Argument::type(Form::class));

        $dataFetcher->getType()->willReturn('sylius_data_fetcher_test_data_fetcher');
        $dataFetcherRegistry->all()->willReturn(['test_data_fetcher' => $dataFetcher]);
        $builder->create('dataFetcherConfiguration', 'sylius_data_fetcher_test_data_fetcher')->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn(Argument::type(Form::class));

        $prototypes = [
            'renderers' => [
                'test_renderer' => Argument::type(Form::class),
                ],
            'dataFetchers' => [
                'test_data_fetcher' => Argument::type(Form::class),
                ],
            ];
        $builder->setAttribute('prototypes', $prototypes)->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_builds_view(
        FormConfigInterface $config,
        FormView $view,
        FormInterface $form,
        FormInterface $formTable,
        FormInterface $formUserRegistration
    ) {
        $prototypes = [
            'dataFetchers' => ['user_registration' => $formUserRegistration],
            'renderers' => ['table' => $formTable],
        ];
        $config->getAttribute('prototypes')->willReturn($prototypes);
        $form->getConfig()->willReturn($config);

        $formTable->createView($view)->shouldBeCalled();
        $formUserRegistration->createView($view)->shouldBeCalled();

        $this->buildView($view, $form, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_report');
    }
}
