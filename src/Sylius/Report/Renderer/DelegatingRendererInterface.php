<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Report\Renderer;

use Sylius\Report\DataFetcher\Data;
use Sylius\Report\Model\ReportInterface;

/**
 * @author Mateusz Zalewski <zaleslaw@.gmail.com>
 */
interface DelegatingRendererInterface
{
    /**
     * @param ReportInterface $subject
     * @param Data            $data
     *
     * @return int
     */
    public function render(ReportInterface $subject, Data $data);
}
