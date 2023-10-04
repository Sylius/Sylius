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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Dashboard;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

final class StatisticsComponent
{
    /** @var array<string, mixed> */
    public array $context;

    public ChannelInterface $channel;

    public function __construct (
        private readonly StatisticsDataProviderInterface $statisticsDataProvider,
    ) {
    }

    #[ExposeInTemplate]
    public function getContext(): array
    {
        return array_merge(
            $this->context,
            [
                ...$this->statisticsDataProvider->getRawData(
                    $this->channel,
                    new \DateTime('-30 days'),
                    new \DateTime(),
                    'day',
                ),
            ]
        );
    }
}
