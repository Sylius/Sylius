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

namespace Sylius\Behat\Element;

use Behat\Mink\Element\DocumentElement as BaseDocumentElement;
use Behat\Mink\Element\NodeElement as BaseNodeElement;

final class DocumentElement extends BaseDocumentElement
{
    public function find($selector, $locator)
    {
        $element = parent::find($selector, $locator);

        if (null === $element) {
            return null;
        }

        return new NodeElement($element->getXpath(), $this->getSession());
    }

    /**
     * @return NodeElement[]
     */
    public function findAll($selector, $locator): array
    {
        $elements = parent::findAll($selector, $locator);

        return array_map(
            fn (BaseNodeElement $element) => new NodeElement($element->getXpath(), $this->getSession()),
            $elements,
        );
    }
}
