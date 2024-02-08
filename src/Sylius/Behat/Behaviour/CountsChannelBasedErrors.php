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
use Behat\Mink\Exception\ElementNotFoundException;

trait CountsChannelBasedErrors
{
    use SessionAccessor;

    /** @throws ElementNotFoundException */
    protected function countChannelErrors(NodeElement $channelCollectionElement, string $channelCode): int
    {
        $errorCountSelector = sprintf('.item[data-tab*="[%s]"] span.label', $channelCode);
        /** @var NodeElement $element */
        $element = $channelCollectionElement->find('css', $errorCountSelector);

        if (null === $element) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'Channel errors count label',
                'css',
                $errorCountSelector,
            );
        }

        return (int) $element->getText();
    }
}
