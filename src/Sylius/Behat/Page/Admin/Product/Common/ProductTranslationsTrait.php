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

namespace Sylius\Behat\Page\Admin\Product\Common;

use Sylius\Behat\Service\DriverHelper;

trait ProductTranslationsTrait
{
    public function getDefinedProductTranslationsElements(): array
    {
        return [
            'field_name' => '[data-test-name="%locale%"]',
            'generate_product_slug_button' => '[data-test-generate-product-slug-button="%localeCode%"]',
            'meta_description' => '[data-test-meta-description="%locale%"]',
            'meta_keywords' => '[data-test-meta-keywords="%locale%"]',
            'name' => '[data-test-name="%locale%"]',
            'product_translation_accordion' => '[data-test-product-translations-accordion="%localeCode%"]',
            'slug' => '[data-test-slug="%locale%"]',
        ];
    }

    public function nameItIn(string $name, string $localeCode): void
    {
        $this->changeTab('translations');
        $this->expandTranslationAccordion($localeCode);

        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);
    }

    public function generateSlug(string $localeCode): void
    {
        $this->getElement('generate_product_slug_button', ['%localeCode%' => $localeCode])->click();
        $this->waitForFormUpdate();
    }

    public function getSlug(string $locale): string
    {
        return $this->getElement('slug', ['%locale%' => $locale])->getValue();
    }

    public function specifySlugIn(string $slug, string $locale): void
    {
        $this->changeTab('translations');

        $this->getElement('slug', ['%locale%' => $locale])->setValue($slug);
    }

    public function setMetaKeywords(string $keywords, string $localeCode): void
    {
        $this->getElement('meta_keywords', ['%locale%' => $localeCode])->setValue($keywords);
    }

    public function setMetaDescription(string $description, string $localeCode): void
    {
        $this->getElement('meta_description', ['%locale%' => $localeCode])->setValue($description);
    }

    private function expandTranslationAccordion(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $translationAccordion = $this->getElement('product_translation_accordion', ['%localeCode%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }

    private function changeTab(string $tabName): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => $tabName])->click();
    }

    public function activateLanguageTab(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $languageTabTitle = $this->getElement('language_tab', ['%localeCode%' => $localeCode]);
        if (!$languageTabTitle->hasClass('active')) {
            $languageTabTitle->click();
        }
    }
}
