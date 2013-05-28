<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Entity;

use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use Gedmo\Translator\TranslationInterface;

/**
 * Product translation entity.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProductTranslation extends AbstractPersonalTranslation implements TranslationInterface
{
    public function getTranslatable()
    {
        return $this->getObject();
    }

    public function setTranslatable($translatable)
    {
        $this->setObject($translatable);
    }

    public function getProperty()
    {
        return $this->getField();
    }

    public function setProperty($property)
    {
        $this->setField($property);
    }

    public function getValue()
    {
        return $this->getContent();
    }

    public function setValue($value)
    {
        $this->setContent($value);
    }
}
