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

namespace Sylius\Behat\Element\Admin\Channel;

use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ExcludeTaxonsFromShowingLowestPriceInputElement extends BaseFormElement implements ExcludeTaxonsFromShowingLowestPriceInputElementInterface
{
    public function __construct(
        Session $session,
        array|MinkParameters $minkParameters,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function excludeTaxon(TaxonInterface $taxon): void
    {
        $excludeTaxonElement = $this->getElement('taxons_excluded_from_showing_lowest_price');

        $this->autocompleteHelper->selectByValue(
            $this->getDriver(),
            $excludeTaxonElement->getXpath(),
            $taxon->getCode(),
        );
        $this->waitForFormUpdate();
    }

    public function removeExcludedTaxon(TaxonInterface $taxon): void
    {
        $excludeTaxonElement = $this->getElement('taxons_excluded_from_showing_lowest_price');

        $this->autocompleteHelper->removeByValue(
            $this->getDriver(),
            $excludeTaxonElement->getXpath(),
            $taxon->getCode(),
        );
        $this->waitForFormUpdate();
    }

    public function hasTaxonExcluded(TaxonInterface $taxon): bool
    {
        return null !== $this
            ->getElement('taxons_excluded_from_showing_lowest_price')
            ->find('css', sprintf('option:selected:contains("%s")', $taxon->getName()))
        ;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'taxons_excluded_from_showing_lowest_price' => '[data-test-taxons-excluded-from-showing-lowest-price]',
        ]);
    }
}
