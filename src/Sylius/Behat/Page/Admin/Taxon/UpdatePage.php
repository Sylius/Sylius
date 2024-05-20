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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Behat\Service\SlugGenerationHelper;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    private array $imageUrls = [];

    public function chooseParent(TaxonInterface $taxon): void
    {
        AutocompleteHelper::chooseValue($this->getSession(), $this->getElement('parent')->getParent(), $taxon->getName());
        JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
    }

    public function describeItAs(string $description, string $languageCode): void
    {
        $this->getDocument()->fillField(sprintf('sylius_taxon_translations_%s_description', $languageCode), $description);
    }

    public function isSlugReadonly(string $languageCode = 'en_US'): bool
    {
        return SlugGenerationHelper::isSlugReadonly(
            $this->getSession(),
            $this->getElement('slug', ['%language%' => $languageCode]),
        );
    }

    public function enableSlugModification(string $languageCode = 'en_US'): void
    {
        SlugGenerationHelper::enableSlugModification(
            $this->getSession(),
            $this->getElement('toggle_taxon_slug_modification_button', ['%locale%' => $languageCode]),
        );
    }

    public function getParent(): string
    {
        return $this->getElement('parent')->getValue();
    }

    public function getSlug(string $languageCode = 'en_US'): string
    {
        return $this->getElement('slug', ['%language%' => $languageCode])->getValue();
    }

    public function getValidationMessageForImage(): string
    {
        $lastImageElement = $this->getLastImageElement();

        $foundElement = $lastImageElement->find('css', '[data-test-validation-error]');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '[data-test-validation-error]');
        }

        return $foundElement->getText();
    }

    public function getValidationMessageForImageAtPlace(int $place): string
    {
        $images = $this->getImageElements();

        $foundElement = $images[$place]->find('css', '[data-test-validation-error]');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '[data-test-validation-error]');
        }

        return $foundElement->getText();
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

        $this->getDocument()->waitFor(10, fn () => $languageTabTitle->hasClass('active'));
    }

    public function enable(): void
    {
        $this->getElement('enabled')->check();
    }

    public function disable(): void
    {
        $this->getElement('enabled')->uncheck();
    }

    public function isEnabled(): bool
    {
        return $this->getElement('enabled')->isChecked();
    }

    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if (!isset($parameters['%language%'])) {
            $parameters['%language%'] = 'en_US';
        }

        return parent::getElement($name, $parameters);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_taxon_code',
            'description' => '#sylius_taxon_translations_en_US_description',
            'enabled' => '#sylius_taxon_enabled',
            'language_tab' => '[data-locale="%locale%"] .title',
            'name' => '#sylius_taxon_translations_en_US_name',
            'parent' => '#sylius_taxon_parent',
            'slug' => '#sylius_taxon_translations_%language%_slug',
            'toggle_taxon_slug_modification_button' => '[data-locale="%locale%"] .toggle-taxon-slug-modification',
        ]);
    }

    private function getLastImageElement(): NodeElement
    {
        $imageElements = $this->getImageElements();

        Assert::notEmpty($imageElements);

        return end($imageElements);
    }

    /**
     * @return NodeElement[]
     */
    private function getImageElements(): array
    {
        $images = $this->getElement('images');

        return $images->findAll('css', 'div[data-form-collection="item"]');
    }
}
