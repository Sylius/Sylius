<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\CatalogPromotion;

use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    public function checkIfScopeConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('products');
    }

    public function checkIfActionConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('amount');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_catalog_promotion_code',
            'endDate' => '#sylius_catalog_promotion_endDate',
            'name' => '#sylius_catalog_promotion_name',
            'products' => '[name="sylius_catalog_promotion[scopes][0][configuration][products]"]',
            'amount' => '[name^="sylius_catalog_promotion[actions][0][configuration]"][name$="[amount]"]',
        ]);
    }
}
