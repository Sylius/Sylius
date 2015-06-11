<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Renderer;

use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\RendererInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sylius\Component\Report\Renderer\DefaultRenderers;

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
            $data = array(
                'report' => $report,
                'values' => $data->getData(),
                'labels' => $data->getLabels(),
                'fields' => array_keys($data->getData()),
            );

            $rendererConfiguration = $report->getRendererConfiguration();

            return $this->templating->renderResponse($rendererConfiguration["template"], array(
                'data' => $data,
                'configuration' => $rendererConfiguration,
            ));
        }

        return $this->templating->renderResponse("SyliusReportBundle::noDataTemplate.html.twig", array(
            'report' => $report,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return DefaultRenderers::TABLE;
    }
}
