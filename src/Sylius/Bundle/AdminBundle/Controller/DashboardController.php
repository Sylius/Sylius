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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class DashboardController
{
    /** @var DashboardStatisticsProviderInterface */
    private $statisticsProvider;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var RouterInterface */
    private $router;

    public function __construct(
        DashboardStatisticsProviderInterface $statisticsProvider,
        ChannelRepositoryInterface $channelRepository,
        EngineInterface $templatingEngine,
        RouterInterface $router
    ) {
        $this->statisticsProvider = $statisticsProvider;
        $this->channelRepository = $channelRepository;
        $this->templatingEngine = $templatingEngine;
        $this->router = $router;
    }

    public function indexAction(Request $request): Response
    {
        $channelCode = $request->query->get('channel');

        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst($channelCode);

        if (null === $channel) {
            return new RedirectResponse($this->router->generate('sylius_admin_channel_create'));
        }

        $statistics = $this->statisticsProvider->getStatisticsForChannel($channel);

        return $this->templatingEngine->renderResponse(
            '@SyliusAdmin/Dashboard/index.html.twig',
            ['statistics' => $statistics, 'channel' => $channel]
        );
    }

    private function findChannelByCodeOrFindFirst(?string $channelCode): ?ChannelInterface
    {
        $channel = null;
        if (null !== $channelCode) {
            $channel = $this->channelRepository->findOneByCode($channelCode);
        }

        if (null === $channel) {
            $channels = $this->channelRepository->findAll();

            $channel = current($channels) === false ? null : current($channels);
        }

        return $channel;
    }
}
