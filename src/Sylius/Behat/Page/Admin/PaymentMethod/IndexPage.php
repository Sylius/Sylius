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

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Webmozart\Assert\Assert;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function countPaymentMethodsWithName(string $name): int
    {
        return count($this->getRowsWithName($name));
    }

    public function getPaymentMethodNamesInOrder(): array
    {
        $names = [];
        foreach ($this->getNameElements() as $nameElement) {
            $names[] = trim($nameElement->getText());
        }

        return $names;
    }

    public function deletePaymentMethodWithName(string $name): void
    {
        $row = $this->getRowWithName($name);

        $row->find('css', '[data-test-modal="delete"] [data-test-confirm-button]')->press();
    }

    public function checkPaymentMethodWithName(string $name): void
    {
        $row = $this->getRowWithName($name);

        $checkbox = $row->find('css', '.form-check-input');
        Assert::notNull($checkbox, sprintf('There is no checkbox for the payment method "%s".', $name));

        $checkbox->check();
    }

    /** @return NodeElement[] */
    private function getRowsWithName(string $name): array
    {
        $rows = [];
        foreach ($this->getBodyRows() as $row) {
            $nameElement = $row->find('css', '.payment-method-list__name');
            if (null !== $nameElement && trim($nameElement->getText()) === $name) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function getRowWithName(string $name): NodeElement
    {
        $rows = $this->getRowsWithName($name);
        Assert::notEmpty($rows, sprintf('There is no payment method with name "%s".', $name));

        return $rows[0];
    }

    /** @return NodeElement[] */
    private function getNameElements(): array
    {
        $nameElements = [];
        foreach ($this->getBodyRows() as $row) {
            $nameElement = $row->find('css', '.payment-method-list__name');
            if (null !== $nameElement) {
                $nameElements[] = $nameElement;
            }
        }

        return $nameElements;
    }

    /** @return NodeElement[] */
    private function getBodyRows(): array
    {
        try {
            $table = $this->getElement('table');
        } catch (ElementNotFoundException) {
            return [];
        }

        return $table->findAll('css', 'tbody > tr');
    }
}
