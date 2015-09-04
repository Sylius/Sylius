<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Report\Renderer;

use Sylius\Component\Report\DataFetcher\Data;
use Sylius\Component\Report\Model\ReportInterface;

interface RendererInterface
{
    /**
     * @param ReportInterface $report
     * @param Data            $data
     *
     * @return string
     */
    public function render(ReportInterface $report, Data $data);

    /**
     * @return string
     */
    public function getType();
}
