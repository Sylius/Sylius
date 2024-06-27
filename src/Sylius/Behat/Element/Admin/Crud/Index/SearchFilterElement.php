<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Admin\Crud\Index;

use FriendsOfBehat\PageObjectExtension\Element\Element;

class SearchFilterElement extends Element implements SearchFilterElementInterface
{
    public function searchWith(string $phrase): void
    {
        $this->getElement('filter_search')->setValue($phrase);
        $this->getElement('filter_button')->press();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_search' => '#criteria_search_value',
            'filter_button' => '[data-test-filter]',
        ]);
    }
}
