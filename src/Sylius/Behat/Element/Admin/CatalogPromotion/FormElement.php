<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Admin\CatalogPromotion;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class FormElement extends Element implements FormElementInterface
{
    public function nameIt(string $name): void
    {
        $this->getElement('name')->setValue($name);
    }

    public function labelIt(string $label, string $localeCode): void
    {
        $this->getElement('label', ['%localeCode%' => $localeCode])->setValue($label);
    }

    public function describeIt(string $description, string $localeCode): void
    {
        $this->getElement('description', ['%localeCode%' => $localeCode])->setValue($description);
    }

    public function checkChannel(string $channelName): void
    {
        $this->getDocument()->checkField($channelName);
    }

    public function uncheckChannel(string $channelName): void
    {
        $this->getDocument()->uncheckField($channelName);
    }

    public function getFieldValueInLocale(string $field, string $localeCode): string
    {
        return $this->getElement($field, ['%localeCode%' => $localeCode])->getValue();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channel' => '#sylius_catalog_promotion_code',
            'description' => '#sylius_catalog_promotion_translations_%localeCode%_description',
            'label' => '#sylius_catalog_promotion_translations_%localeCode%_label',
            'name' => '#sylius_catalog_promotion_name',
        ]);
    }
}
