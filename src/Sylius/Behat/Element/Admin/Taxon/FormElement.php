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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    use ChecksCodeImmutability;
    use SpecifiesItsField;

    public function __construct(
        Session $session,
        array|MinkParameters $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function getCode(): string
    {
        return $this->getElement('code')->getValue();
    }

    public function nameIt(string $name, string $localeCode): void
    {
        $this->expandTranslationAccordion($localeCode);

        $this->getElement('name', ['%locale_code%' => $localeCode])->setValue($name);
    }

    public function slugIt(string $slug, string $localeCode): void
    {
        $this->getElement('slug', ['%locale_code%' => $localeCode])->setValue($slug);
    }

    public function generateSlug(string $localeCode): void
    {
        $this->getElement('generate_slug_button', ['%locale_code%' => $localeCode])->click();
        $this->waitForFormUpdate();
    }

    public function describeItAs(string $description, string $localeCode): void
    {
        $this->getElement('description', ['%locale_code%' => $localeCode])->setValue($description);
    }

    public function getParent(): string
    {
        return $this->getElement('parent')->getValue();
    }

    public function chooseParent(TaxonInterface $taxon): void
    {
        $this->autocompleteHelper->removeByName($this->getDriver(), $this->getElement('parent')->getXpath(), '');
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('parent')->getXpath(),
            $taxon->getName(),
        );
        $this->waitForFormUpdate();
    }

    public function removeCurrentParent(): void
    {
        $this->autocompleteHelper->clear($this->getDriver(), $this->getElement('parent')->getXpath());
        $this->waitForFormUpdate();
    }

    public function getTranslationFieldValue(string $element, string $localeCode): string
    {
        return $this->getElement($element, ['%locale_code%' => $localeCode])->getValue();
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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'description' => '[data-test-description="%locale_code%"]',
            'enabled' => '[data-test-enabled]',
            'form' => '[data-live-name-value="sylius_admin:taxon:form"]',
            'generate_slug_button' => '[data-test-generate-slug-button="%locale_code%"]',
            'name' => '[data-test-name="%locale_code%"]',
            'parent' => '[data-test-parent]',
            'slug' => '[data-test-slug="%locale_code%"]',
            'translation_accordion' => '[data-test-taxon-translations-accordion="%locale_code%"]',
        ]);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    private function expandTranslationAccordion(string $localeCode): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $translationAccordion = $this->getElement('translation_accordion', ['%locale_code%' => $localeCode]);

        if ($translationAccordion->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $translationAccordion->click();
    }
}
