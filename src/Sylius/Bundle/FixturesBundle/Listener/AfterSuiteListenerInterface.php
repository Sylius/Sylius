<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Listener;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface AfterSuiteListenerInterface extends ListenerInterface
{
    /**
     * @param SuiteEvent $suiteEvent
     * @param array $options
     */
    public function afterSuite(SuiteEvent $suiteEvent, array $options);
}
