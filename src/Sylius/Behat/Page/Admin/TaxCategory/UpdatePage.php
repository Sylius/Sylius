<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\TaxCategory;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    /**
     * @var array
     */
    protected $elements = [
        'name' => '#sylius_tax_category_name',
        'code' => '#sylius_tax_category_code',
        'description' => '#sylius_tax_category_description',
    ];

    /**
     * {@inheritdoc}
     */
    public function hasResourceValues(array $parameters)
    {
        foreach ($parameters as $element => $value) {
            if($this->getElement($element)->getValue() !== (string) $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }
}
