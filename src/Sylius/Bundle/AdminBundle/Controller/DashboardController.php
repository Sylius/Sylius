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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final readonly class DashboardController
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private Environment $templatingEngine,
        private RouterInterface $router,
    ) {
        trigger_deprecation(
            'sylius/admin-bundle',
            '1.14',
            sprintf(
                'Passing an instance of "%s" as the fourth argument is deprecated. It will be removed in Sylius 2.0.',
                StatisticsDataProviderInterface::class,
            ),
        );
    }

    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->findChannelByCodeOrFindFirst($request->query->has('channel') ? (string) $request->query->get('channel') : null);

        if (null === $channel) {
            return new RedirectResponse($this->router->generate('sylius_admin_channel_create'));
        }

        return new Response($this->templatingEngine->render('@SyliusAdmin/dashboard/index.html.twig', [
            'channel' => $channel,
        ]));
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
