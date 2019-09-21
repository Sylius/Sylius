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

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class AdministratorStatusController
{
    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(
        EngineInterface $templatingEngine
    ) {
        $this->templatingEngine = $templatingEngine;
    }

    public function indexAction(Request $request): Response
    {
        return $this->templatingEngine->renderResponse(
            '@SyliusAdmin/AdministratorStatus/index.html.twig',
            [
                'phpVersion' => phpversion(),
                'modules' => get_loaded_extensions(),
                'syliusVersion' => Kernel::VERSION,
            ]
        );
    }
}
