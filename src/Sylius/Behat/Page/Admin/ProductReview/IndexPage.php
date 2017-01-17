<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductReview;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept(array $parameters)
    {
        $this->getActionButtonsField($parameters)->pressButton('Accept');
    }

    /**
     * {@inheritdoc}
     */
    public function reject(array $parameters)
    {
        $this->getActionButtonsField($parameters)->pressButton('Reject');
    }

    /**
     * @param array $parameters
     *
     * @return NodeElement
     */
    private function getActionButtonsField(array $parameters)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, $parameters);

        return $tableAccessor->getFieldFromRow($table, $row, 'actions');
    }
}
