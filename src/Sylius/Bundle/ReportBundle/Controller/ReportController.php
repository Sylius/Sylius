<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Report\Renderer\TableRenderer;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReportController extends ResourceController
{
    public function renderAction(Request $request)
    {
        $renderer = $this->get("sylius.form.type.renderer.chart");
        return $renderer->render(array(), array());
    }
}
