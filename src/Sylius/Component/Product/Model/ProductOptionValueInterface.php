<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductOptionValueInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    /**
     * @return ProductOptionInterface
     */
    public function getOption();

    /**
     * @param ProductOptionInterface $option
     */
    public function setOption(ProductOptionInterface $option = null);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     */
    public function setValue($value);

    /**
     * Proxy method to access the presentation of real option object.
     *
     * @return string The code of object
     */
    public function getOptionCode();

    /**
     * Proxy method to access the name of real option object.
     *
     * @return string The name of object
     */
    public function getName();
}
