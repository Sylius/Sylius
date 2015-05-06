<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ImportExportBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Prophecy\Argument;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chrusciel <lukasz.chrusciel@lakion.com>
 */
class ExportProfileTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry)
    {
        $this->beConstructedWith('Sylius\Component\ImportExport\Model\ExportProfile', array('sylius'), $readerRegistry, $writerRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ImportExportBundle\Form\Type\ExportProfileType');
    }

    function it_should_be_abstract_resource_type_object()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_build_form_with_proper_fields(
        FormBuilderInterface $builder,
        FormFactoryInterface $factory,
        $readerRegistry,
        $writerRegistry,
        ReaderInterface $reader,
        WriterInterface $writer
    ) {
        $builder->getFormFactory()->willReturn($factory);
        $builder->add('name', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('code', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('description', 'textarea', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('reader', 'sylius_export_reader_choice', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('writer', 'sylius_export_writer_choice', Argument::any())->shouldBeCalled()->willReturn($builder);

        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildReaderFormListener'))->shouldBeCalled()->willReturn($builder);
        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildWriterFormListener'))->shouldBeCalled()->willReturn($builder);

        $reader->getType()->willReturn('test_reader');
        $readerRegistry->all()->willReturn(array('test_reader' => $reader));

        $builder->create('readerConfiguration', 'sylius_test_reader_reader')->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn(Argument::type('Symfony\Component\Form\Form'));

        $writer->getType()->willReturn('test_writer');
        $writerRegistry->all()->willReturn(array('test_writer' => $writer));

        $builder->create('writerConfiguration', 'sylius_test_writer_writer')->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn(Argument::type('Symfony\Component\Form\Form'));

        $prototypes = array(
            'readers' => array(
                'test_reader' => Argument::type('Symfony\Component\Form\Form'),
            ),
            'writers' => array(
                'test_writer' => Argument::type('Symfony\Component\Form\Form'),
            ),
        );

        $builder->setAttribute('prototypes', $prototypes)->shouldBeCalled();
        $this->buildForm($builder, array());
    }

    function it_builds_view(
        FormConfigInterface $config,
        FormView $view,
        FormInterface $form,
        FormInterface $formUser,
        FormInterface $formCsv
    ) {
        $prototypes = array(
           'reader' => array('table' => $formUser),
           'writer' => array('csv' => $formCsv),
       );

        $config->getAttribute('prototypes')->willReturn($prototypes);
        $form->getConfig()->willReturn($config);

        $formUser->createView($view)->shouldBeCalled();
        $formCsv->createView($view)->shouldBeCalled();

        $this->buildView($view, $form, array());
    }
    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_export_profile');
    }
}
