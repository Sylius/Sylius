<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\AdminBundle\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Controller\DashboardController;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class DashboardControllerTest extends TestCase
{
    private MockObject&ChannelRepositoryInterface $channelRepository;

    private Environment&MockObject $templatingEngine;

    private MockObject&RouterInterface $router;

    private DashboardController $dashboardController;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->templatingEngine = $this->createMock(Environment::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->dashboardController = new DashboardController(
            $this->channelRepository,
            $this->templatingEngine,
            $this->router,
        );
    }

    public function testRendersDashboardForFirstEnabledChannelByDefault(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $request = new Request();

        $this->channelRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true], ['id' => 'ASC'], 1)
            ->willReturn([$channel])
        ;
        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->with('@SyliusAdmin/dashboard/index.html.twig', ['channel' => $channel])
            ->willReturn('dashboard')
        ;

        $response = ($this->dashboardController)($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('dashboard', $response->getContent());
    }

    public function testRedirectsToChannelCreationWhenThereIsNoEnabledChannel(): void
    {
        $request = new Request();

        $this->channelRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['enabled' => true], ['id' => 'ASC'], 1)
            ->willReturn([])
        ;
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_admin_channel_create')
            ->willReturn('/admin/channels/new')
        ;
        $this->templatingEngine->expects($this->never())->method('render');

        $response = ($this->dashboardController)($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/admin/channels/new', $response->getTargetUrl());
    }
}
