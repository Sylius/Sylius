<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ReportBundle\Renderer;

use Sylius\Report\DataFetcher\Data;
use Sylius\Report\Model\ReportInterface;
use Sylius\Report\Renderer\DefaultRenderers;
use Sylius\Report\Renderer\RendererInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class TableRenderer implements RendererInterface
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function render(ReportInterface $report, Data $data)
    {
        if (null !== $data->getData()) {
            $data = [
                'report' => $report,
                'values' => $data->getData(),
                'labels' => $data->getLabels(),
                'fields' => array_keys($data->getData()),
            ];

            $rendererConfiguration = $report->getRendererConfiguration();

            return $this->templating->render($rendererConfiguration['template'], [
                'data' => $data,
                'configuration' => $rendererConfiguration,
            ]);
        }

        return $this->templating->render('SyliusReportBundle::noDataTemplate.html.twig', [
            'report' => $report,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return DefaultRenderers::TABLE;
    }
}
