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

use Sylius\Component\Translation\Model\TranslatableInterface;
use Sylius\Component\Translation\Model\TranslatableTrait;

/**
 * Foo translatable entity.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslatableFoo implements TranslatableInterface
{
    use TranslatableTrait;

    public static function getTranslationClass()
    {
        return  'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo';
    }
}
