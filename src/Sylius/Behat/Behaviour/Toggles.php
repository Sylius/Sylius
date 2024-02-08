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

namespace Sylius\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

trait Toggles
{
    abstract protected function getToggleableElement(): NodeElement;

    /**
     * @throws \RuntimeException If already enabled
     */
    public function enable(): void
    {
        $toggleableElement = $this->getToggleableElement();
        $this->assertCheckboxState($toggleableElement, false);

        $toggleableElement->check();
    }

    /**
     * @throws \RuntimeException If already disabled
     */
    public function disable(): void
    {
        $toggleableElement = $this->getToggleableElement();
        $this->assertCheckboxState($toggleableElement, true);

        $toggleableElement->uncheck();
    }

    /**
     * @param bool $expectedState
     *
     * @throws \RuntimeException
     */
    private function assertCheckboxState(NodeElement $toggleableElement, $expectedState)
    {
        if ($toggleableElement->isChecked() !== $expectedState) {
            throw new \RuntimeException(sprintf(
                "Toggleable element state is '%s' but expected '%s'.",
                $toggleableElement->isChecked() ? 'true' : 'false',
                $expectedState ? 'true' : 'false',
            ));
        }
    }
}
