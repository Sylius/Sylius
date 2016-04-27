<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelThemeListener
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @param ThemeRepositoryInterface $themeRepository
     */
    public function __construct(ThemeRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $channel = $eventArgs->getObject();

        if (!$channel instanceof ChannelInterface) {
            return;
        }

        $objectManager = $eventArgs->getObjectManager();

        $channelReflection = $objectManager->getClassMetadata(get_class($channel))->getReflectionClass();

        $channelThemeIdReflection = $channelReflection->getProperty('themeId');
        $channelThemeIdReflection->setAccessible(true);

        $themeId = $channelThemeIdReflection->getValue($channel);

        /** @var ThemeInterface $theme */
        $theme = $this->themeRepository->find($themeId); // TODO: Lazy loading
        $channel->setTheme($theme);
    }
}
