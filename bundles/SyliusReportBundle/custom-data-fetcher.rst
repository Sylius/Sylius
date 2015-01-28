Adding custom data fetcher
==========================

SyliusReportBundle has some default data fetchers implemented, however, obviously they can be insufficient for some e-commerce systems. This chapter shows step-by-step way, how to add new data fetcher to make reports customization easier and more corresponding to your needs.

Create custom data fetcher class
--------------------------------

First step is creation of our new data fetcher class. It should be placed in ``Sylius\Bundle\ReportBundle\DataFetcher`` namespace and to provide proper logic it has to implement ``Sylius\Component\Report\DataFetcher\DataFetcherInterface``. Because of implementation, data fetcher class must provide two methods:
    - ``fetch(array $configuration)``, which fetches data
    - ``getType``, which returns unique name of data fetcher

.. note::

   It is highly recommended to place all data fetchers types as constants in ``Sylius\Component\Report\Renderer\DefaultDataFetchers``

.. caution::

    Data fetcher has to return Data class(``Sylius\Component\Report\DataFetcher\Data``), that is part of Report component

.. code-block:: php

    <?php

    namespace Sylius\Bundle\ReportBundle\DataFetcher;

    use Sylius\Component\Report\DataFetcher\DataFetcherInterface;
    use Sylius\Component\Report\DataFetcher\Data;

    class CustomDataFetcher implements DataFetcherInterface
    {

        /**
         * {@inheritdoc}
         */
        public function fetch(array $configuration)
        {
            $data = new Data();

            //Some operations, that will provide data for your renderer

            return $data;
        }

        /**
         * {@inheritdoc}
         */
        public function getType()
        {
            return DefaultDataFetchers::CUSTOM;
        }
    }


Create data fetcher configuration type
--------------------------------------

Each data fetcher has its own, specific cofiguration form, which is added to main report form. It has to be specified in ``Sylius\Bundle\ReportBundle\Form\Type\DataFetcher`` namespace and extends ``Symfony\Component\Form\AbstractType``. To be able to configure our data fetcher in form, this class should override ``buildForm(FormBuilderInterface $builder, array $options)`` method. It should also have ``getName`` method, that returns data fetcher string identifier.

.. code-block:: php

    <?php

    namespace Sylius\Bundle\ReportBundle\Form\Type\DataFetcher;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;

    class CustomConfigurationType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add(
                    //custom options you want to provide in your custom data fetcher
                ))
            ;
        }

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'sylius_data_fetcher_custom';
        }
    }


Register custom data fetcher class as service
---------------------------------------------

To be able to use our new data fetcher, it must be registered in ReportBundle services.xml file. We should take care of two classes we just created, means ``CustomDataFetcher`` and ``CustomConfigurationType``. They have to be tagged with proper tags, to be visible for CompilerPass.

.. code-block:: xml

    <parameters>
        //other parameters
        <parameter key="sylius.form.type.data_fetcher.custom.class">Sylius\Bundle\ReportBundle\Form\Type\DataFetcher\CustomType</parameter>
        <parameter key="sylius.report.data_fetcher.custom.class">Sylius\Bundle\ReportBundle\DataFetcher\CustomDataFetcher</parameter>
    </parameters>

    <services>
        //other services
        <service id="sylius.form.type.data_fetcher.custom" class="%sylius.form.type.data_fetcher.custom.class%">
            <tag name="form.type" alias="sylius_data_fetcher_custom" />
        </service>

        <service id="sylius.report.data_fetcher.custom" class="%sylius.report.data_fetcher.custom.class%">
            <argument type="service" id="sylius.repository.order" />
            <tag name="sylius.report.data_fetcher" fetcher="custom" label="Custom data fetcher" />
        </service>
    </services>


Summary
-------

With this three simple steps, you can create your own, great data fetcher. Renderers can not wait for it.