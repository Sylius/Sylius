<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

use Sylius\Component\Variation\Model\VariableInterface;

/**
 * Variants choice list.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantChoiceList extends ObjectChoiceList
{
    /**
     * Constructor.
     *
     * @param VariableInterface $variable
     */
    public function __construct(VariableInterface $variable)
    {
        parent::__construct($variable->getVariants(), 'presentation', array(), null, 'id');
    }
}
