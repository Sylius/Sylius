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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Product\Model\ProductTranslationInterface as BaseProductTranslationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ProductTranslationInterface extends BaseProductTranslationInterface
{
    /**
     * @return string
     */
    public function getShortDescription();

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);
}
