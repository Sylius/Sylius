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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ChannelNameExtension extends AbstractExtension
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_channel_name', [$this, 'getChannelNameByCode']),
        ];
    }

    public function getChannelNameByCode(string $code): string
    {
        return $this->channelRepository->findOneByCode($code)->getName();
    }
}
