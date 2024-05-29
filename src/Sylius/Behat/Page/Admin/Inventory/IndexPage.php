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

namespace Sylius\Behat\Page\Admin\Inventory;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        array|\ArrayAccess $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $tableAccessor, $routeName);
    }

    public function specifyFilterType(string $field, string $type): void
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $type);
    }

    public function specifyFilterValue(string $field, string $value): void
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $value);
    }

    public function filterByProduct(string $productName): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('product_filter')->getXpath(),
            $productName,
        );

        $this->waitForFormUpdate();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_filter' => '#criteria_product',
        ]);
    }
}
