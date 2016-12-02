<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\IndexPage as CrudIndexPage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class IndexPage extends CrudIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterByTaxon($taxonName)
    {
        $this->getElement('taxon_filter', ['%taxon%' => $taxonName])->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'taxon_filter' => '.item a:contains("%taxon%")',
        ]);
    }
}
