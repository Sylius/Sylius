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

use Sylius\Component\Report\Model\ReportInterface;

/**
 * @author Mateusz Zalewski <zaleslaw@.gmail.com>
 */
interface DelegatingRendererInterface 
{
    /**
     *
     * @param ReportInterface $subject
     * @param array           $context
     *
     * @return integer
     */
    public function render(ReportInterface $subject, array $context = array());    
}