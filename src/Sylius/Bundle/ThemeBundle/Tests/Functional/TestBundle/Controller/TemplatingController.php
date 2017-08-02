<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class TemplatingController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

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
