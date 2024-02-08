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

        /** @var NodeElement $option */
        foreach ($options->findAll('css', 'li') as $option) {
            if ($option->getText() === $optionName) {
                return true;
            }
        }

        return false;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'options' => '#options ul',
        ]);
    }
}
