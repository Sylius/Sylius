<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductAssociationType;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function specifyFilterType($field, $type)
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $type);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyFilterValue($field, $value)
    {
        $this->getDocument()->fillField(sprintf('criteria_%s_value', $field), $value);
    }
}
