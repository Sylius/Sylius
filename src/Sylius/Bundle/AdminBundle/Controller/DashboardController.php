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

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class DashboardController
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private Environment $templatingEngine,
        private RouterInterface $router,
        private ?StatisticsDataProviderInterface $statisticsDataProvider = null,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst($request->query->has('channel') ? (string) $request->query->get('channel') : null);

        if (null === $channel) {
            return new RedirectResponse($this->router->generate('sylius_admin_channel_create'));
        }

        return new Response($this->templatingEngine->render('@SyliusAdmin/Dashboard/index.html.twig', [
            'channel' => $channel,
        ]));
    }

    public function getRawData(Request $request): Response
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst((string) $request->query->get('channelCode'));

        if (null === $channel) {
            return new RedirectResponse($this->router->generate('sylius_admin_channel_create'));
        }

        return new JsonResponse(
            $this->statisticsDataProvider->getRawData(
                $channel,
                (new \DateTime((string) $request->query->get('startDate'))),
                (new \DateTime((string) $request->query->get('endDate'))),
                (string) $request->query->get('interval'),
            ),
        );
    }

    private function findChannelByCodeOrFindFirst(?string $channelCode): ?ChannelInterface
    {
        if (null !== $channelCode) {
            $channel = $this->channelRepository->findOneByCode($channelCode);
            Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

            return $channel;
        }

        $channel = $this->channelRepository->findBy([], ['id' => 'ASC'], 1)[0] ?? null;
        Assert::nullOrIsInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }
}
