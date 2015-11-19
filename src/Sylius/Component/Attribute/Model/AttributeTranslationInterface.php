<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface AttributeTranslationInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getPresentation();

    /**
     * @param string $presentation
     */
    public function setPresentation($presentation);
}
