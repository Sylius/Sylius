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

namespace Sylius\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class OptionsElement extends Element implements OptionsElementInterface
{
    public function isOptionDefined(string $optionName): bool
    {
        $options = $this->getElement('options');

        return $options->has('css', sprintf('div:contains("%s")', $optionName));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'options' => '[data-test-options]',
        ]);
    }
}
