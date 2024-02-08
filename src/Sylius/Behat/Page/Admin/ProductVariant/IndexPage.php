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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getOnHandQuantityFor(ProductVariantInterface $productVariant): int
    {
        return (int) $this->getElement('on_hand_quantity', ['%id%' => $productVariant->getId()])->getText();
    }

    public function getOnHoldQuantityFor(ProductVariantInterface $productVariant): int
    {
        try {
            return (int) $this->getElement('on_hold_quantity', ['%id%' => $productVariant->getId()])->getText();
        } catch (ElementNotFoundException) {
            return 0;
        }
    }

    public function setPosition(string $name, int $position): void
    {
        /** @var NodeElement $productVariantsRow */
        $productVariantsRow = $this->getElement('table')->find('css', sprintf('tbody > tr:contains("%s")', $name));
        Assert::notNull($productVariantsRow, 'There are no row with given product variant\'s name!');

        $productVariantPosition = $productVariantsRow->find('css', '.sylius-product-variant-position');
        Assert::notNull($productVariantPosition, 'There are no position field in given row!');
        $productVariantPosition->setValue($position);
    }

    public function savePositions(): void
    {
        $this->getElement('save_configuration_button')->press();

        $this->getDocument()->waitFor(5, fn () => null === $this->getElement('save_configuration_button')->find('css', '.loading'));
    }

    public function countItemsWithNoName(): int
    {
        return count($this->getElement('table')->findAll('css', '[data-test-missing-translation-paragraph]'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'on_hand_quantity' => '.onHand[data-product-variant-id="%id%"]',
            'on_hold_quantity' => '.onHold[data-product-variant-id="%id%"]',
            'save_configuration_button' => '.sylius-save-position',
        ]);
    }
}
