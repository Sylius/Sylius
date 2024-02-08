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

namespace Sylius\Behat\Page\Admin\ProductAssociationType;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    public function nameItIn(string $name, string $language): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_association_type_translations_%s_name', $language),
            $name,
        );
    }

    public function nameIt($name): void
    {
        $this->getElement('name')->setValue($name);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_association_type_code',
            'name' => '#sylius_product_association_type_translations_en_US_name',
        ]);
    }
}
