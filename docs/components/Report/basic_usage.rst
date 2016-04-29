Basic Usage
===========

.. note::
    First of all you need to register your data fetcher and renderer.
    For more detailed information go to :doc:`/components/Registry/basic_usage`.

.. _component_report_data-fetcher_delegating-data-fetcher:

DelegatingDataFetcher
---------------------

This service allows you to delegate fetching to your custom registered service.
Your data fetcher class should implement `DataFetcherInterface`_.

.. _DataFetcherInterface: http://api.sylius.org/Sylius/Component/Report/DataFetcher/DataFetcherInterface.html

.. code-block:: php

    <?php

    use Sylius\Component\Report\DataFetcher\DataFetcherInterface;

    /**
     * Sales total data fetcher
     */
    class SalesTotalDataFetcher implements DataFetcherInterface
    {
        const TYPE = 'sales_total';

        /**
         * {@inheritdoc}
         */
        public function fetch(array $configuration)
        {
            // TODO: Implement fetch() method.
        }

        /**
         * {@inheritdoc}
         */
        public function getType()
        {
            return self::TYPE;
        }
    }

.. code-block:: php

    <?php

    use Sylius\Component\Report\DataFetcher\Data;
    use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;
    use Sylius\Component\Report\Renderer\DefaultRenderers;
    use Sylius\Component\Report\DataFetcher\DelegatingDataFetcher;
    use Sylius\Component\Registry\ServiceRegistry;
    use Sylius\Component\Report\Model\Report;
    use Sylius\Component\Report\Renderer\RendererInterface;
    use Sylius\Component\Report\Model\ReportInterface;



    $salesTotalDataFetcher = new SalesTotalDataFetcher();

    $report = new Report();
    $report->setDataFetcher(SalesTotalDataFetcher::TYPE);
    $report->setRenderer(TableRenderer::TYPE);

    // Let's register our data fetcher.
    $serviceRegistry = new ServiceRegistry(DataFetcherInterface::class);
    $serviceRegistry->register(SalesTotalDataFetcher::TYPE, $salesTotalDataFetcher);
    $delegatingDataFetcher = new DelegatingDataFetcher($serviceRegistry);

    $delegatingDataFetcher->fetch($report); // Output depends on implementation of your data fetcher.

.. _component_report_renderer_delegating-renderer:

DelegatingRenderer
------------------

This service allows you to delegate rendering to your custom registered service.
Your renderer should implement `RendererInterface`_.

.. _RendererInterface: http://api.sylius.org/Sylius/Component/Report/Renderer/RendererInterface.html

.. code-block:: php

    <?php

    use Sylius\Component\Report\Renderer\RendererInterface;

    class TableRenderer implements RendererInterface
    {
        const TYPE = 'table';

        /**
         * {@inheritdoc}
         */
        public function render(ReportInterface $report, Data $data)
        {
            // TODO: Implement render() method.
        }

        /**
         * {@inheritdoc}
         */
        public function getType()
        {
            return self::TYPE;
        }
    }

.. code-block:: php

    <?php

    use Sylius\Component\Report\DataFetcher\Data;
    use Sylius\Component\Report\Renderer\DelegatingRenderer;
    use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;
    use Sylius\Component\Report\Renderer\DefaultRenderers;
    use Sylius\Component\Registry\ServiceRegistry;
    use Sylius\Component\Report\Model\Report;
    use Sylius\Component\Report\Model\ReportInterface;

    $tableRenderer = new TableRenderer();

    $report = new Report();
    $report->setDataFetcher(DefaultDataFetchers::SALES_TOTAL);
    $report->setRenderer(DefaultRenderers::TABLE);

    $data = new Data(); // Your data fetched from data fetcher.

    // Let's register our table renderer.
    $serviceRegistry = new ServiceRegistry(RendererInterface::class);
    $serviceRegistry->register(TableRenderer::TYPE, $tableRenderer);
    $delegatingRenderer = new DelegatingRenderer($serviceRegistry);

    $delegatingRenderer->render($report, $data); // Output depends on implementation of your renderer.
