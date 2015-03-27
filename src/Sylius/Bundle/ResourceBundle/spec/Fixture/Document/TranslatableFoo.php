<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Fixture\Document;

use Sylius\Component\Translation\Model\AbstractTranslatable;

/**
 * Foo translatable document.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class TranslatableFoo extends AbstractTranslatable
{
    protected function getTranslationClass()
    {
        return 'spec\Sylius\Bundle\ResourceBundle\Fixture\Document\Foo';
    }
}
