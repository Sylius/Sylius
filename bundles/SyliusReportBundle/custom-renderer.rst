Adding custom renderer
=======================

SyliusReportBundle has some default renderers implemented, however, obviously they can be insufficient for some e-commerce systems. This chapter shows step-by-step way, how to add new renderer to make reports customization easier and more corresponding to our needs.

Create custom renderer class
-------------------------------

First step is creation of our new renderer class. It should be placed in ``Sylius\Bundle\ReportBundle\Renderer`` namespace and must implement ``Sylius\Component\Report\Renderer\RendererInterface``. Because of implementation, renderer class must provide two methods:
    - ``render(ReportInterface $report, Data $data)``, which generates response based on given report and data fetcher data
    - ``getType``, which returns unique name of renderer

.. note::

   It is highly recommended to place all renderers types as constants in ``Sylius\Component\Report\Renderer\DefaultRenderers``

.. code-block:: php

    <?php

    namespace Sylius\Bundle\ReportBundle\Renderer;

    use Sylius\Component\Report\Model\ReportInterface;
    use Sylius\Component\Report\Renderer\RendererInterface;
    use Sylius\Component\Report\Renderer\DefaultRenderers;

    class CustomRenderer implements RendererInterface
    {
        public function render(ReportInterface $report, Data $data)
        {
            //Some operations on given data, that returns Response, which
            //is next catched by controller and used to display view
        }

        public function getType()
        {
            return DefaultRenderers::CUSTOM;
        }
    }

Create renderer configuration type
-------------------------------------

Each renderer has its own, specific cofiguration form, which is added to main report form. It has to be specified in ``Sylius\Bundle\ReportBundle\Form\Type`` namespace and extends ``Symfony\Component\Form\AbstractType``. To be able to configure our renderer in form, this class should override ``buildForm(FormBuilderInterface $builder, array $options)`` method. It should also have ``getName`` method, that returns renderer string identifier.

.. code-block:: php

    <?php

    namespace Sylius\Bundle\ReportBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    /**
     * Custom renderer configuration form type
     */
    class CustomConfigurationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('template', 'choice', array(
                    'label' => 'sylius.form.report.renderer.template',  //Default template label - "template", it can be any string or message you want
                    'choices' => array(
                        'SyliusReportBundle:Custom:default.html.twig' => 'Default',
                    ),
                ))
            ;
        }

        public function getName()
        {
            return 'sylius_renderer_custom';
        }
    }


Register custom rednerer class as service
-------------------------------------------

To be able to use our new renderer, it must be registered in ReportBundle services.xml file. We should take care of two classes we just created, means ``CustomRenderer`` and ``CustomConfigurationType``. They have to be tagged with proper tags, to be visible for CompilerPass.

.. code-block:: xml

    <parameters>
        <parameter key="sylius.form.type.renderer.custom.class">Sylius\Bundle\ReportBundle\Renderer\CustomRenderer</parameter>
        <parameter key="sylius.form.type.renderer.custom_configuration.class">Sylius\Bundle\ReportBundle\Form\Type\CustomConfigurationType</parameter>
    </parameters>

    <services>
        <service id="sylius.form.type.renderer.custom" class="%sylius.form.type.renderer.custom.class%">
            <tag name="sylius.report.renderer" renderer="custom" label="Custom renderer" />
        </service>
        <service id="sylius.form.type.report.renderer.custom_configuration" class="%sylius.form.type.report.renderer.custom_configuration.class%">
            <tag name="form.type" alias="sylius_renderer_custom" />
        </service>
    </services>


Summary
----------

With this three simple steps, you can create your own, great renderer, which allows you to display fetched data however you want.