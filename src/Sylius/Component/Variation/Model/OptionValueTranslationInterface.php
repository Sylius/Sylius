<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Vincenzo Provenza <vincenzo.provenza89@gmail.com>
 */
interface OptionValueTranslationInterface extends ResourceInterface
{
    /**
     * The name displayed to user.
     *
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     */
    public function setValue($value);
}
