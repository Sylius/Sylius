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

namespace Sylius\Behat\Element\Admin\Product;

use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\DriverHelper;

final class ProductTranslationsFormElement extends BaseFormElement implements ProductTranslationsFormElementInterface
{
    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab();
        $this->expandTranslationAccordion($localeCode);

        $this->getElement('name', ['%locale_code%' => $localeCode])->setValue($name);
    }

    public function hasNameInLocale(string $name, string $localeCode): bool
    {
        return $this->getElement('name', ['%locale_code%' => $localeCode])->getValue() === $name;
    }

    public function generateSlug(string $localeCode): void
    {
        $this->getElement('generate_product_slug_button', ['%locale_code%' => $localeCode])->click();
        $this->waitForFormUpdate();
    }

    public function getSlug(string $locale): string
    {
        return $this->getElement('slug', ['%locale_code%' => $locale])->getValue();
    }

    public function specifySlugIn(string $slug, string $locale): void
    {
        $this->changeTab();

        $this->getElement('slug', ['%locale_code%' => $locale])->setValue($slug);
    }

    public function setMetaKeywords(string $keywords, string $localeCode): void
    {
        $this->getElement('meta_keywords', ['%locale_code%' => $localeCode])->setValue($keywords);
    }

    public function setMetaDescription(string $description, string $localeCode): void
    {
        $this->getElement('meta_description', ['%locale_code%' => $localeCode])->setValue($description);
    }

    public function activateLanguageTab(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%locale_code%' => $localeCode]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }

    protected function getDefinedElements(): array
    {
        return [
            'field_name' => '[data-test-name="%locale_code%"]',
            'form' => 'form',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%locale_code%"]',
            'meta_description' => '[data-test-meta-description="%locale_code%"]',
            'meta_keywords' => '[data-test-meta-keywords="%locale_code%"]',
            'name' => '[data-test-name="%locale_code%"]',
            'product_translation_accordion' => '[data-test-product-translations-accordion="%locale_code%"]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
            'slug' => '[data-test-slug="%locale_code%"]',
        ];
    }

    private function expandTranslationAccordion(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $translationAccordion = $this->getElement('product_translation_accordion', ['%locale_code%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }

    private function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'translations'])->click();
    }
}
