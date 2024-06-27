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

namespace Sylius\Behat\Page\Admin\Taxon;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Core\Model\TaxonInterface;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function countTaxons(): int
    {
        return count($this->getLeaves());
    }

    public function countTaxonsByName(string $name): int
    {
        $matchedLeavesCounter = 0;
        $leaves = $this->getLeaves();
        foreach ($leaves as $leaf) {
            if (str_contains($leaf->getText(), $name)) {
                ++$matchedLeavesCounter;
            }
        }

        return $matchedLeavesCounter;
    }

    public function deleteTaxonOnPageByName(string $name): void
    {
        $leaves = $this->getLeaves();
        /** @var NodeElement $leaf */
        foreach ($leaves as $leaf) {
            if ($leaf->find('css', '.sylius-tree__title')->getText() === $name) {
                $leaf->find('css', '.sylius-tree__action__trigger')->click();
                JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
                $leaf->find('css', '.sylius-tree__action button')->press();
                $this->getElement('confirmation_button')->click();

                return;
            }
        }

        throw new ElementNotFoundException($this->getDriver(), 'Delete button');
    }

    public function hasTaxonWithName(string $name): bool
    {
        return 0 !== $this->countTaxonsByName($name);
    }

    public function getLeaves(?TaxonInterface $parentTaxon = null): array
    {
        return $this->getDocument()->findAll('css', '.sylius-tree__item');
    }

    public function moveUpTaxon(string $name): void
    {
        $taxonElement = $this->getElement('tree_item', ['%taxon%' => $name]);
        $treeAction = $taxonElement->getParent()->getParent()->find('css', '.sylius-tree__action');
        $treeAction->click();
        JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        $treeAction->find('css', '.sylius-taxon-move-up .up')->click();
        JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
    }

    public function moveDownTaxon(string $name): void
    {
        $taxonElement = $this->getElement('tree_item', ['%taxon%' => $name]);
        $treeAction = $taxonElement->getParent()->getParent()->find('css', '.sylius-tree__action');
        $treeAction->click();
        JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        $treeAction->find('css', '.sylius-taxon-move-down .down')->click();
        JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
    }

    public function getFirstTaxonOnTheList(): string
    {
        return $this->getLeaves()[0]->getText();
    }

    public function getLastTaxonOnTheList(): string
    {
        $leaves = $this->getLeaves();

        return $leaves[count($leaves) - 1]->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'confirmation_button' => '#confirmation-button',
            'tree' => '.sylius-tree',
            'tree_item' => '.sylius-tree__item a:contains("%taxon%")',
        ]);
    }
}
