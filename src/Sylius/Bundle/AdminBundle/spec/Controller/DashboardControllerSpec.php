<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Controller\DashboardController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin DashboardController
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardControllerSpec extends ObjectBehavior
{
    function let(EngineInterface $templatingEngine)
    {
        $this->beConstructedWith($templatingEngine);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AdminBundle\Controller\DashboardController');
    }

    function it_renders_dashboard(Request $request, EngineInterface $templatingEngine, Response $response)
    {
        $templatingEngine->renderResponse('SyliusAdminBundle:Dashboard:index.html.twig')->willReturn($response);

        $this->indexAction($request)->shouldReturn($response);
    }
}
