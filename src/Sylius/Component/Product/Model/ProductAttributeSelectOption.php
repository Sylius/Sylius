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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\AttributeSelectOption as BaseSelectOption;
use Sylius\Component\Attribute\Model\AttributeSelectOptionTranslationInterface;

/**
 * @author Asier Marqués <asier@simettric.com>
 */
class ProductAttributeSelectOption extends BaseSelectOption
    implements ProductAttributeSelectOptionInterface
{

    /**
     * @return AttributeSelectOptionTranslationInterface
     */
    protected function createTranslation(): AttributeSelectOptionTranslationInterface
    {
        return new ProductAttributeSelectOptionTranslation();
    }
}
