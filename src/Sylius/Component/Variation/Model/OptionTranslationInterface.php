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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface OptionTranslationInterface extends ResourceInterface
{
    /**
     * The name displayed to user.
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);
}
