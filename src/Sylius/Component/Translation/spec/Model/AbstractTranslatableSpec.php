<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Translation\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Sylius\Component\Translation\Model\AbstractTranslation;
use Sylius\Component\Translation\Model\TranslatableInterface;
use Sylius\Component\Translation\Model\TranslationInterface;

class AbstractTranslatableSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Sylius\Component\Translation\Model\ConcreteTranslatable');
    }

    function it_is_translatable()
    {
        $this->shouldImplement(TranslatableInterface::class);
    }

    function it_initializes_translattion_collection_by_default()
    {
        $this->getTranslations()->shouldHaveType(Collection::class);
    }

    function it_adds_translation(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->hasTranslation($translation)->shouldReturn(true);
    }

    function it_removes_translation(TranslationInterface $translation)
    {
        $this->addTranslation($translation);
        $this->removeTranslation($translation);

        $this->hasTranslation($translation)->shouldReturn(false);
    }

    function its_current_locale_is_mutable()
    {
        $this->setCurrentLocale('en_US');
        $this->getCurrentLocale()->shouldReturn('en_US');
    }

    function its_fallback_locale_is_mutable()
    {
        $this->setFallbackLocale('en_US');
        $this->getFallbackLocale()->shouldReturn('en_US');
    }

    function it_throws_exception_if_no_locale_has_been_set()
    {
        $this->shouldThrow(\RuntimeException::class)->duringTranslate();
    }

    function it_translates_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en_US');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');

        $this->translate()->shouldReturn($translation);
    }

    function it_creates_new_empty_translation_properly()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
        $this->translate()->shouldHaveType('spec\Sylius\Component\Translation\Model\ConcreteTranslatableTranslation');
    }

    function it_clones_new_translation_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();
        $translation->acmeProperty = 'acmeProp';

        $this->addTranslation($translation);
        $this->setCurrentLocale('en');

        $translation = $this->translate();
        $translation->shouldImplement('Sylius\Component\Translation\Model\TranslationInterface');
        $translation->acmeProperty->shouldBe('acmeProp');
    }
}

class ConcreteTranslatable extends AbstractTranslatable
{
    public static function getTranslationClass()
    {
        return  'spec\Sylius\Component\Translation\Model\ConcreteTranslatableTranslation';
    }
}

class ConcreteTranslatableTranslation extends AbstractTranslation
{
}
