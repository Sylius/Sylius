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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsField;

    public function getValidationMessageForAction(): string
    {
        $actionForm = $this->getLastCollectionItem('actions');

        $foundElement = $actionForm->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    public function checkIfRuleConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('count');
    }

    public function checkIfActionConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('amount');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_promotion_code',
            'form' => '[data-live-name-value="SyliusAdmin.Promotion.Form"]',
            'minimum' => '#sylius_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_min',
            'maximum' => '#sylius_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_max',
            'rules' => '#sylius_promotion_rules',
            'count' => '#sylius_promotion_rules_0_configuration_count',
            'amount' => '#sylius_promotion_actions_0_configuration_WEB-US_amount',
        ]);
    }

    private function getLastCollectionItem(string $collection): NodeElement
    {
        $items = $this->getCollectionItems($collection);

        Assert::notEmpty($items);

        return end($items);
    }

    /**
     * @return NodeElement[]
     */
    private function getCollectionItems(string $collection): array
    {
        $items = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        Assert::isArray($items);

        return $items;
    }
}
