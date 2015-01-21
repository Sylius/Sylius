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
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Form;
use Prophecy\Argument;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Bundle\ReportBundle\Renderer\TableRenderer;
use Sylius\Bundle\ReportBundle\Renderer\ChartRenderer;

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

    // function it_build_form_with_proper_fields(FormBuilder $builder, $dataFetcherRegistry, $rendererRegistry, TableRenderer $tableRenderer, ChartRenderer $chartRenderer)
    // {
    //     $builder->addEventSubscriber(Argument::any())->willReturn($builder);

    //     $builder->add('name', 'text', Argument::any())->willReturn($builder);
    //     $builder->add('description', 'textarea', Argument::any())->willReturn($builder);
    //     $builder->add('renderer', 'sylius_renderer_choice', Argument::any())->willReturn($builder);
    //     $builder->add('dataFetcher', 'sylius_data_fetcher_choice', Argument::any())->willReturn($builder);

    //     $rendererRegistry->all()->willReturn(array('table' => $tableRenderer, 'chart' => $chartRenderer));
    //     $builder->create('rendererConfiguration')
    // }

    // function it_builds_view(FormView $view, FormView $tableRendererView, FormView $chartRendererView, FormView $userRegistrationDataFetcherView, FormInterface $form, FormConfigInterface $config, FormInterface $formInterface, Form $formTable, Form $formChart, Form $formUserRegistration)
    // {
    //     $form->getConfig()->willReturn($config);
    //     $prototypes = array(
    //         'renderers' => array('table' => $formTable, 'chart' => $formChart),
    //         'dataFetchers' => array('user_registration' => $formUserRegistration)
    //     );
    //     $config->getAttributes('prototypes')->willReturn($prototypes);

    //     $formTable->createView($view)->shouldReturn($formTable);
    //     $formChart->createView($view)->shouldReturn($formChart);
    //     $formUserRegistration->createView($view)->shouldReturn($formUserRegistration);

    //     $this->buildView($view, $formInterface, array());
    // }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_report');
    }
}