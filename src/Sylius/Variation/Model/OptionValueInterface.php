<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Variation\Model;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OptionValueInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    /**
     * @return OptionInterface
     */
    public function getOption();

    /**
     * @param OptionInterface $option
     */
    public function setOption(OptionInterface $option = null);

    /**
     * Get internal value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Set internal value.
     *
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
