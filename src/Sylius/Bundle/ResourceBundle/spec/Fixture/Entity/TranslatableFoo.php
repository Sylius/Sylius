<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Fixture\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;

/**
 * Foo translatable entity.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslatableFoo extends AbstractTranslatable
{
    protected function getTranslationEntityClass(){
        return  'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo';
    }
}
