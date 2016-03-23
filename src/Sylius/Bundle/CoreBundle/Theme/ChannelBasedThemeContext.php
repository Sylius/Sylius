<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Theme;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
final class ChannelBasedThemeContext implements ThemeContextInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getTheme()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->getChannel();

            return $channel->getTheme();
        } catch (ChannelNotFoundException $exception) {
            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->getChannel();

            return $channel->getCode();
        } catch (ChannelNotFoundException $exception) {
            return null;
        }
    }

    private function getChannel()
    {
        return $this->channelContext->getChannel();
    }
}
