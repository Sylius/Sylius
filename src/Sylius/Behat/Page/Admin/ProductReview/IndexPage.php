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

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Webmozart\Assert\Assert;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
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
        $this->getElement('filter_state')->selectOption($state);
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
            'filter_state' => '#criteria_status',
        ]);
    }
}
