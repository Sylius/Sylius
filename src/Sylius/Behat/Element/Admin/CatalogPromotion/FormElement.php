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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Webmozart\Assert\Assert;

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

    public function changeEnableTo(bool $enabled): void
    {
        $this->getElement('enabled')->setValue($enabled);
    }

    public function checkChannel(string $channelName): void
    {
        $this->getDocument()->checkField($channelName);
    }

    public function uncheckChannel(string $channelName): void
    {
        $this->getDocument()->uncheckField($channelName);
    }

    public function addRule(): void
    {
        $this->addCollectionElement('rules', 'add_rule_button');
    }

    public function addAction(): void
    {
        $this->addCollectionElement('actions', 'add_action_button');
    }

    public function chooseLastRuleVariants(array $variantCodes): void
    {
        $lastRule = $this->getElement('last_rule');

        $lastRule->find('css', 'input[type="hidden"]')->setValue(implode(',', $variantCodes));
    }

    public function specifyLastActionDiscount(string $discount): void
    {
        $lastAction = $this->getElement('last_action');

        $lastAction->find('css', 'input')->setValue($discount);
    }

    public function getFieldValueInLocale(string $field, string $localeCode): string
    {
        return $this->getElement($field, ['%localeCode%' => $localeCode])->getValue();
    }

    public function getLastRuleVariantCodes(): array
    {
        $lastRule = $this->getElement('last_rule');

        return explode(',', $lastRule->find('css', 'input[type="hidden"]')->getValue());
    }

    public function getLastActionDiscount(): string
    {
        $lastAction = $this->getElement('last_action');

        return $lastAction->find('css', 'input')->getValue();
    }

    public function getValidationMessageForAction(): string
    {
        $foundElement = $this->getDocument()->find('css', '.sylius-validation-error');

        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    public function removeAllActions(): void
    {
        $deleteButtons = $this->getDocument()->findAll('css', 'data-form-collection="delete"');

        foreach ($deleteButtons as $deleteButton) {
            $deleteButton->click();
        }
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'actions' => '#actions',
            'add_action_button' => '#actions [data-form-collection="add"]',
            'add_rule_button' => '#rules [data-form-collection="add"]',
            'channel' => '#sylius_catalog_promotion_code',
            'description' => '#sylius_catalog_promotion_translations_%localeCode%_description',
            'enabled' => '#sylius_catalog_promotion_enabled',
            'label' => '#sylius_catalog_promotion_translations_%localeCode%_label',
            'last_action' => '#actions [data-form-collection="item"]:last-child',
            'last_rule' => '#rules [data-form-collection="item"]:last-child',
            'name' => '#sylius_catalog_promotion_name',
            'rules' => '#rules',
        ]);
    }

    private function addCollectionElement(string $collectionElement, string $buttonElement): void
    {
        $count = count($this->getCollectionItems($collectionElement));

        $this->getElement($buttonElement)->click();

        $this->getDocument()->waitFor(5, function () use ($count, $collectionElement) {
            return $count + 1 === count($this->getCollectionItems($collectionElement));
        });
    }

    /** @return NodeElement[] */
    private function getCollectionItems(string $collection): array
    {
        $items = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        Assert::isArray($items);

        return $items;
    }
}
