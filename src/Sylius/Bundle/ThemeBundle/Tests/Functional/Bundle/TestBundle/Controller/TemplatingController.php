<?php

namespace Sylius\Bundle\ThemeBundle\Tests\Functional\Bundle\TestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplatingController extends ContainerAware
{
    public function renderTemplateAction($templateName)
    {
        return $this->container->get('templating')->renderResponse($templateName);
    }
}
