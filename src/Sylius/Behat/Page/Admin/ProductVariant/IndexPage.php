<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOnHandQuantityFor(ProductVariantInterface $productVariant)
    {
        return (int) $this->getElement('on_hand_quantity', ['%id%' => $productVariant->getId()])->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHoldQuantityFor(ProductVariantInterface $productVariant)
    {
        try {
            return (int) $this->getElement('on_hold_quantity', ['%id%' => $productVariant->getId()])->getText();
        } catch (ElementNotFoundException $exception) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($name, $position)
    {
        /** @var NodeElement $productVariantsRow */
        $productVariantsRow = $this->getElement('table')->find('css', sprintf('tbody > tr:contains("%s")', $name));
        Assert::notNull($productVariantsRow, 'There are no row with given product variant\'s name!');

        $productVariantPosition = $productVariantsRow->find('css', '.sylius-product-variant-position');
        Assert::notNull($productVariantPosition, 'There are no position field in given row!');
        $productVariantPosition->setValue($position);
    }

    public function savePositions()
    {
        $this->getElement('save_configuration_button')->press();

        $this->getDocument()->waitFor(5, function () {
            return false === $this->getElement('save_configuration_button')->hasClass('loading');
        });
    }


    /**
     * {@inheritdoc}
     */
    public function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'on_hand_quantity' => '.onHand[data-product-variant-id="%id%"]',
            'on_hold_quantity' => '.onHold[data-product-variant-id="%id%"]',
            'save_configuration_button' => '.sylius-save-position',
        ]);
    }
}
