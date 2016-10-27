<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\SeleniumExtension;

use Behat\Mink\Mink;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SeleniumSessionRestarter implements EventSubscriberInterface
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @param Mink $mink
     */
    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AfterSuiteTested::BEFORE => ['stopSession', 128],
        ];
    }

    public function stopSession()
    {
        if ($this->mink->hasSession('selenium2') && $this->mink->isSessionStarted('selenium2')) {
            $this->mink->getSession('selenium2')->stop();
        }
    }
}
