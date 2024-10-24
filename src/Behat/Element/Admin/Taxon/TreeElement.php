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

namespace Sylius\Behat\Element\Admin\Taxon;

use FriendsOfBehat\PageObjectExtension\Element\Element as BaseElement;

final class TreeElement extends BaseElement implements TreeElementInterface
{
    public function getTaxonsNames(): array
    {
        $treeTaxons = $this->getElement('tree_taxons');
        $taxons = [];

        foreach ($treeTaxons->findAll('css', '[data-test-tree-taxon]') as $taxon) {
            $taxons[] = $taxon->getText();
        }

        return $taxons;
    }

    public function countTaxons(): int
    {
        return count($this->getElement('tree_taxons')->findAll('css', '[data-test-tree-taxon]'));
    }

    public function isTaxonOnTheList(string $taxonName): bool
    {
        $taxons = $this->getElement('tree_taxons')->findAll('css', '[data-test-tree-taxon]');

        foreach ($taxons as $taxon) {
            if ($taxonName === $taxon->getText()) {
                return true;
            }
        }

        return false;
    }

    public function getFirstTaxonOnTheList(): string
    {
        return $this->getElement('first_tree_taxon')->getText();
    }

    public function getLastTaxonOnTheList(): string
    {
        return $this->getElement('last_tree_taxon')->getText();
    }

    public function moveUpTaxon(string $name): void
    {
        $this->getElement('tree_taxon_actions', ['%name%' => $name])->click();
        $this->getElement('tree_taxon_move_up', ['%name%' => $name])->click();
        $this->waitForUpdate();
    }

    public function moveDownTaxon(string $name): void
    {
        $this->getElement('tree_taxon_actions', ['%name%' => $name])->click();
        $this->getElement('tree_taxon_move_down', ['%name%' => $name])->click();
        $this->waitForUpdate();
    }

    public function deleteTaxon(string $name): void
    {
        $this->getElement('tree_taxon_actions', ['%name%' => $name])->click();
        $this->getElement('tree_taxon_delete', ['%name%' => $name])->click();
        $this->waitForUpdate('tree_taxon_delete_component');
        $this->getElement('confirm_delete_button', ['%name%' => $name])->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'confirm_delete_button' => '[data-test-tree-taxons] [data-test-tree-taxon="%name%"] [data-test-delete-modal] [data-test-confirm-button]',
            'first_tree_taxon' => '[data-test-tree-taxons] [data-test-tree-taxon]:first-child',
            'last_tree_taxon' => '[data-test-tree-taxons] [data-test-tree-taxon]:last-child',
            'tree_taxons' => '[data-test-tree-taxons]',
            'tree_taxon_actions' => '[data-test-tree-taxons] [data-test-tree-taxon="%name%"] [data-test-actions]',
            'tree_taxon_component' => '[data-live-name-value="sylius_admin:taxon:tree"]',
            'tree_taxon_delete' => '[data-test-tree-taxons] [data-test-tree-taxon="%name%"] [data-test-delete]',
            'tree_taxon_delete_component' => '[data-live-name-value="sylius_admin:taxon:delete"]',
            'tree_taxon_move_down' => '[data-test-tree-taxons] [data-test-tree-taxon="%name%"] [data-test-move-down]',
            'tree_taxon_move_up' => '[data-test-tree-taxons] [data-test-tree-taxon="%name%"] [data-test-move-up]',
        ]);
    }

    protected function waitForUpdate(string $element = 'tree_taxon_component'): void
    {
        $elementComponent = $this->getElement($element);
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the treeTaxonComponent sets the busy attribute
        $elementComponent->waitFor(1500, function () use ($elementComponent) {
            return !$elementComponent->hasAttribute('busy');
        });
    }
}
