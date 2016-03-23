<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
trait Toggles
{
    use ElementAccessor;

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $enabled = $this->getElement('enabled');
        $this->assertPriorStateOfToggleableElement($enabled, false);

        $enabled->check();
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $enabled = $this->getElement('enabled');
        $this->assertPriorStateOfToggleableElement($enabled, true);

        $enabled->uncheck();
    }

    /**
     * @param NodeElement $toggleableElement
     * @param bool $expectedState
     *
     * @throws \RuntimeException
     */
    private function assertPriorStateOfToggleableElement(NodeElement $toggleableElement, $expectedState)
    {
        if ($toggleableElement->isChecked() !== $expectedState) {
            throw new \RuntimeException('Toggleable element state %s but expected %s', $toggleableElement->isChecked(), $expectedState);
        }
    }
}
