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

namespace Sylius\Behat\Element\Admin\CatalogPromotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\TabsHelper;
use Sylius\Component\Core\Model\ChannelInterface;
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

    public function prioritizeIt(int $priority): void
    {
        $this->getElement('priority')->setValue($priority);
    }

    public function changeEnableTo(bool $enabled): void
    {
        $this->getElement('enabled')->setValue($enabled);
    }

    public function checkChannel(string $channelName): void
    {
        $this->getDocument()->checkField($channelName);
    }

    public function setExclusiveness(bool $isExclusive): void
    {
        $this->getElement('exclusive')->setValue($isExclusive);
    }

    public function uncheckChannel(string $channelName): void
    {
        $this->getDocument()->uncheckField($channelName);
    }

    public function specifyStartDate(\DateTimeInterface $startDate): void
    {
        $timestamp = $startDate->getTimestamp();

        $this->getElement('start_date')->setValue(date('Y-m-d', $timestamp));
    }

    public function specifyEndDate(\DateTimeInterface $endDate): void
    {
        $timestamp = $endDate->getTimestamp();

        $this->getElement('end_date')->setValue(date('Y-m-d', $timestamp));
    }

    public function addScope(): void
    {
        $this->addCollectionElement('scopes', 'add_scope_button');
    }

    public function addAction(): void
    {
        $this->addCollectionElement('actions', 'add_action_button');
    }

    public function chooseScopeType(string $type): void
    {
        $lastScope = $this->getElement('last_scope');

        $lastScope->selectFieldOption('Type', $type);
    }

    public function chooseActionType(string $type): void
    {
        $lastAction = $this->getElement('last_action');

        $lastAction->selectFieldOption('Type', $type);
    }

    public function chooseLastScopeCodes(array $codes): void
    {
        $lastScope = $this->getElement('last_scope');

        $lastScope->find('css', 'input[type="hidden"]')->setValue(implode(',', $codes));
    }

    public function specifyLastActionDiscount(string $discount): void
    {
        $lastAction = $this->getElement('last_action');

        $lastAction->find('css', 'input')->setValue($discount);
    }

    public function specifyLastActionDiscountForChannel(string $discount, ChannelInterface $channel): void
    {
        $lastAction = $this->getElement('last_action');

        TabsHelper::switchTab($this->getSession(), $lastAction, $channel->getCode());

        $lastAction->find('css', sprintf('[id$="%s_amount"]', $channel->getCode()))->setValue($discount);
    }

    public function getFieldValueInLocale(string $field, string $localeCode): string
    {
        return $this->getElement($field, ['%localeCode%' => $localeCode])->getValue();
    }

    public function getLastScopeCodes(): array
    {
        $lastScope = $this->getElement('last_scope');

        return explode(',', $lastScope->find('css', 'input[type="hidden"]')->getValue());
    }

    public function getLastActionDiscount(): string
    {
        $lastAction = $this->getElement('last_action');

        return $lastAction->find('css', 'input')->getValue();
    }

    public function getLastActionFixedDiscount(ChannelInterface $channel): string
    {
        $lastAction = $this->getElement('last_action');

        TabsHelper::switchTab($this->getSession(), $lastAction, $channel->getCode());

        return $lastAction->find('css', sprintf('[id$="%s_amount"]', $channel->getCode()))->getValue();
    }

    public function getValidationMessage(): string
    {
        $foundElement = $this->getDocument()->find('css', '.sylius-validation-error');

        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    public function hasValidationMessage(string $message): bool
    {
        $validationElements = $this->getDocument()->findAll('css', '.sylius-validation-error');

        foreach ($validationElements as $validationElement) {
            if ($validationElement->getText() === $message) {
                return true;
            }
        }

        return false;
    }

    public function hasOnlyOneValidationMessage(string $message): bool
    {
        $validationElements = $this->getDocument()->findAll('css', '.sylius-validation-error');

        $counter = 0;
        foreach ($validationElements as $validationElement) {
            if ($validationElement->getText() === $message) {
                ++$counter;
            }
        }

        return $counter === 1;
    }

    public function removeAllActions(): void
    {
        $deleteButtons = $this->getDocument()->findAll('css', '#actions [data-form-collection="delete"]');

        foreach ($deleteButtons as $deleteButton) {
            $deleteButton->click();
        }
    }

    public function removeAllScopes(): void
    {
        $deleteButtons = $this->getDocument()->findAll('css', '#scopes [data-form-collection="delete"]');

        foreach ($deleteButtons as $deleteButton) {
            $deleteButton->click();
        }
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'actions' => '#actions',
            'add_action_button' => '#actions [data-form-collection="add"]',
            'add_scope_button' => '#scopes [data-form-collection="add"]',
            'description' => '#sylius_catalog_promotion_translations_%localeCode%_description',
            'enabled' => '#sylius_catalog_promotion_enabled',
            'exclusive' => '#sylius_catalog_promotion_exclusive',
            'end_date' => '#sylius_catalog_promotion_endDate_date',
            'label' => '#sylius_catalog_promotion_translations_%localeCode%_label',
            'last_action' => '#actions [data-form-collection="item"]:last-child',
            'last_scope' => '#scopes [data-form-collection="item"]:last-child',
            'name' => '#sylius_catalog_promotion_name',
            'priority' => '#sylius_catalog_promotion_priority',
            'scopes' => '#scopes',
            'start_date' => '#sylius_catalog_promotion_startDate_date',
        ]);
    }

    private function addCollectionElement(string $collectionElement, string $buttonElement): void
    {
        $count = count($this->getCollectionItems($collectionElement));

        $this->getElement($buttonElement)->click();

        $this->getDocument()->waitFor(5, fn () => $count + 1 === count($this->getCollectionItems($collectionElement)));
    }

    /** @return NodeElement[] */
    private function getCollectionItems(string $collection): array
    {
        $items = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        Assert::isArray($items);

        return $items;
    }
}
