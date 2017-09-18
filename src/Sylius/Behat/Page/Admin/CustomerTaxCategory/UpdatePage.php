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

namespace Sylius\Behat\Page\Admin\CustomerTaxCategory;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\DescribesIt;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use NamesIt;
    use DescribesIt;

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->getElement('description')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_customer_tax_category_code',
            'description' => '#sylius_customer_tax_category_description',
            'name' => '#sylius_customer_tax_category_name',
        ]);
    }
}
