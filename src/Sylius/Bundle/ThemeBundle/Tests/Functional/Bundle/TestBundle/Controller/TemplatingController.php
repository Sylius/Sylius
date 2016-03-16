<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplatingController extends ContainerAware
{
    /**
     * @param string $templateName
     *
     * @return Response
     */
    public function renderTemplateAction($templateName)
    {
        return $this->container->get('templating')->renderResponse($templateName);
    }
}
