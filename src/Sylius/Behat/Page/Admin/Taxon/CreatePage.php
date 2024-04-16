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
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsField;

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

    public function describeItAs(string $description, string $languageCode): void
    {
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_description', $languageCode), $description);
    }

    public function hasTaxonWithName(string $name): bool
    {
        return 0 !== $this->countTaxonsByName($name);
    }

    public function nameIt(string $name, string $languageCode): void
    {
        $this->activateLanguageTab($languageCode);
        $this->getElement('name', ['%language%' => $languageCode])->setValue($name);

        if (DriverHelper::isJavascript($this->getDriver())) {
            SlugGenerationHelper::waitForSlugGeneration(
                $this->getSession(),
                $this->getElement('slug', ['%language%' => $languageCode]),
            );
        }
    }

    public function specifySlug(string $slug, string $languageCode): void
    {
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_slug', $languageCode), $slug);
    }

    public function attachImage(string $path, ?string $type = null): void
    {
        $filesPath = $this->getParameter('files_path');

        $this->getDocument()->find('css', '[data-form-collection="add"]')->click();

        $imageForm = $this->getLastImageElement();
        $imageForm->fillField('Type', $type);
        $imageForm->find('css', 'input[type="file"]')->attachFile($filesPath . $path);
    }

    public function getLeaves(?TaxonInterface $parentTaxon = null): array
    {
        return $this->getDocument()->findAll('css', '.sylius-tree__item');
    }

    public function activateLanguageTab(string $locale): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale%' => $locale]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
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

    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if (!isset($parameters['%language%'])) {
            $parameters['%language%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_taxon_code',
            'confirmation_button' => '#confirmation-button',
            'description' => '#sylius_taxon_translations_en_US_description',
            'images' => '#sylius_taxon_images',
            'language_tab' => '[data-locale="%locale%"] .title',
            'name' => '#sylius_taxon_translations_%language%_name',
            'slug' => '#sylius_taxon_translations_%language%_slug',
            'tree' => '.sylius-tree',
            'tree_item' => '.sylius-tree__item a:contains("%taxon%")',
        ]);
    }

    private function getLastImageElement(): NodeElement
    {
        $images = $this->getElement('images');
        $items = $images->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
