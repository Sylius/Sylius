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

namespace Sylius\Behat\Page\Admin\ProductReview;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function accept(array $parameters): void
    {
        $this->getActionButtonsField($parameters)->pressButton('Accept');
    }

    public function reject(array $parameters): void
    {
        $this->getActionButtonsField($parameters)->pressButton('Reject');
    }

    private function getActionButtonsField(array $parameters): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, $parameters);

        return $tableAccessor->getFieldFromRow($table, $row, 'actions');
    }
}
