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

namespace Sylius\Behat\Page\Admin\ProductReview;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

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

    public function accept(array $parameters): void
    {
        $this->changeState('accept', $parameters);
    }

    public function reject(array $parameters): void
    {
        $this->changeState('reject', $parameters);
    }

    public function filterByState(string $state): void
    {
        $this->getElement('state_filter')->selectOption($state);
    }

    public function filterByTitle(string $phrase): void
    {
        $this->getElement('title_filter')->setValue($phrase);
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

    private function changeState(string $state, array $parameters): void
    {
        $action = $this->getActionsForResource($parameters)->find('css', sprintf('[data-test-action="%s"]', $state));
        Assert::notNull($action, sprintf('There is no "%s" action available for this resource', $state));

        $action->find('css', 'button')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_filter' => '#criteria_product',
            'state_filter' => '#criteria_status',
            'title_filter' => '#criteria_title_value',
        ]);
    }
}
