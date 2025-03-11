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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class ShopPreviewComponent
{
    use HookableComponentTrait;

    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(protected readonly ChannelRepositoryInterface $channelRepository)
    {
    }

    /**
     * @return array<string, ChannelInterface>
     */
    #[ExposeInTemplate(name: 'channels')]
    public function getChannels(): array
    {
        return $this->channelRepository->findEnabled();
    }
}
