<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\EventListener;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class ThemeSynchronizationListener
{
    /**
     * @var ThemeSynchronizerInterface
     */
    private $themeSynchronizer;

    /**
     * @param ThemeSynchronizerInterface $themeSynchronizer
     */
    public function __construct(ThemeSynchronizerInterface $themeSynchronizer)
    {
        $this->themeSynchronizer = $themeSynchronizer;
    }

    /**
     * @param GenericEvent $event
     */
    public function synchronizeTheme(GenericEvent $event)
    {
        $this->themeSynchronizer->synchronize($this->getTheme($event));
    }

    /**
     * @param GenericEvent $event
     */
    public function synchronize(GenericEvent $event)
    {
        $this->themeSynchronizer->synchronize();
    }

    /**
     * @param GenericEvent $event
     *
     * @return ThemeInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function getTheme(GenericEvent $event)
    {
        $theme = $event->getSubject();

        if (!$theme instanceof ThemeInterface) {
            throw new UnexpectedTypeException($theme, ThemeInterface::class);
        }

        return $theme;
    }
}
