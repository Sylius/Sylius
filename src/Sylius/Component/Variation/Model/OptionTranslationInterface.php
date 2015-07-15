<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Object option translation interface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface OptionTranslationInterface
{
    /**
     * The name displayed to user.
     *
     * @return string
     */
    public function getPresentation();

    /**
     * Set presentation.
     *
     * @param string $presentation
     */
    public function setPresentation($presentation);
}
