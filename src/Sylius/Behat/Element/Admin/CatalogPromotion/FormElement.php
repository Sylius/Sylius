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
use Behat\Mink\Session;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Service\TabsHelper;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function nameIt(string $name): void
    {
        $this->getElement('name')->setValue($name);
    }

    public function labelIt(string $label, string $localeCode): void
    {
        $this->getElement('label', ['%locale_code%' => $localeCode])->setValue($label);
    }

    public function describeIt(string $description, string $localeCode): void
    {
        $this->getElement('description', ['%locale_code%' => $localeCode])->setValue($description);
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
        $this->getElement('channels')->checkField($channelName);
    }

    public function setExclusiveness(bool $isExclusive): void
    {
        $this->getElement('exclusive')->setValue($isExclusive);
    }

    public function uncheckChannel(string $channelName): void
    {
        $this->getElement('channels')->uncheckField($channelName);
    }

    public function specifyStartDate(\DateTimeInterface $startDate): void
    {
        $timestamp = $startDate->getTimestamp();

        $this->getElement('start_date_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('start_date_time')->setValue(date('H:i', $timestamp));
    }

    public function specifyEndDate(\DateTimeInterface $endDate): void
    {
        $timestamp = $endDate->getTimestamp();

        $this->getElement('end_date_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('end_date_time')->setValue(date('H:i', $timestamp));
    }

    public function addScope(string $type): void
    {
        $this->getElement('add_scope_button', ['%type%' => $type])->press();
        $this->waitForFormUpdate();
    }

    public function addAction(string $type): void
    {
        $this->getElement('add_action_button', ['%type%' => $type])->press();
        $this->waitForFormUpdate();
    }

    public function selectScopeOption(array $values): void
    {
        $lastScope = $this->getElement('last_scope');
        foreach ($values as $value) {
            $this->autocompleteHelper->selectByValue(
                $this->getDriver(),
                $lastScope->find('css', 'select')->getXpath(),
                $value,
            );
        }

        $this->waitForFormUpdate();
    }

    public function fillActionOption(string $option, string $value): void
    {
        $lastAction = $this->getElement('last_action');

        $lastAction->fillField($option, $value);
    }

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void
    {
        $lastAction = $this->getElement('last_action');

        TabsHelper::switchTab($this->getSession(), $lastAction, $channelCode);

        $lastAction->find('css', sprintf('[id$="_configuration_%s"]', $channelCode))->fillField($option, $value);
    }

    public function getLastScopeCodes(): array
    {
        $lastScope = $this->getElement('last_scope');

        return array_map(
            fn (NodeElement $element) => $element->getValue(),
            $lastScope->findAll('css', 'option[selected="selected"]'),
        );
    }

    public function getLastActionOption(string $option): string
    {
        $lastAction = $this->getElement('last_action');

        return $lastAction->findField($option)->getValue();
    }

    public function getLastActionOptionForChannel(string $channelCode, string $option): string
    {
        $lastAction = $this->getElement('last_action');

        TabsHelper::switchTab($this->getSession(), $lastAction, $channelCode);

        return $lastAction->find('css', sprintf('[id$="_configuration_%s"]', $channelCode))->findField($option)->getValue();
    }

    public function checkIfScopeConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('last_scope');
    }

    public function checkIfActionConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('last_action');
    }

    public function getFieldValueInLocale(string $field, string $localeCode): string
    {
        return $this->getElement($field, ['%locale_code%' => $localeCode])->getValue();
    }

    public function getValidationMessages(): array
    {
        $errors = $this->getElement('form')->findAll('css', '.alert-danger');

        return array_map(fn (NodeElement $element) => $element->getText(), $errors);
    }

    public function removeScopeOption(array $values): void
    {
        $lastScope = $this->getElement('last_scope');
        foreach ($values as $value) {
            $this->autocompleteHelper->removeByValue(
                $this->getDriver(),
                $lastScope->find('css', 'select')->getXpath(),
                $value,
            );
        }

        $this->waitForFormUpdate();
    }

    public function removeLastAction(): void
    {
        $this->getElement('last_action')->find('css', '[data-test-delete-action]')->click();
    }

    public function removeLastScope(): void
    {
        $this->getElement('last_scope')->find('css', '[data-test-delete-action]')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_action_button' => '[data-test-actions] [data-test-add-%type%]',
            'add_scope_button' => '[data-test-scopes] [data-test-add-%type%]',
            'channels' => '#sylius_admin_catalog_promotion_channels',
            'description' => '[name="sylius_admin_catalog_promotion[translations][%locale_code%][description]"]',
            'enabled' => '#sylius_admin_catalog_promotion_enabled',
            'end_date_date' => '#sylius_admin_catalog_promotion_endDate_date',
            'end_date_time' => '#sylius_admin_catalog_promotion_endDate_time',
            'exclusive' => '#sylius_admin_catalog_promotion_exclusive',
            'label' => '[name="sylius_admin_catalog_promotion[translations][%locale_code%][label]"]',
            'last_action' => '[data-test-actions] [data-test-entry-row]:last-child',
            'last_scope' => '[data-test-scopes] [data-test-entry-row]:last-child',
            'name' => '#sylius_admin_catalog_promotion_name',
            'priority' => '#sylius_admin_catalog_promotion_priority',
            'start_date_date' => '#sylius_admin_catalog_promotion_startDate_date',
            'start_date_time' => '#sylius_admin_catalog_promotion_startDate_time',
        ]);
    }
}
