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
use Sylius\Behat\Service\JQueryHelper;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
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
}
