<?php

namespace spec\Sylius\Component\Translation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Translation\Model\TranslationInterface;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Sylius\Component\Translation\Model\AbstractTranslation;

class AbstractTranslatableSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Sylius\Component\Translation\Model\ConcreteTranslatable');
    }

    function it_is_translatable()
    {
        $this->shouldImplement('Sylius\Component\Translation\Model\TranslatableInterface');
    }

    function it_initializes_translattion_collection_by_default()
    {
        $this->getTranslations()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_adds_translation(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation)->shouldReturn($this);
        $this->hasTranslation($translation)->shouldReturn(true);
    }

    function it_removes_translation(TranslationInterface $translation)
    {
        $this->addTranslation($translation);
        $this->removeTranslation($translation)->shouldReturn($this);

        $this->hasTranslation($translation)->shouldReturn(false);
    }

    function its_current_locale_is_mutable()
    {
        $this->setCurrentLocale('en')->shouldReturn($this);
        $this->getCurrentLocale()->shouldReturn('en');
    }

    function its_current_translation_is_mutable(TranslationInterface $translation)
    {
        $this->setCurrentTranslation($translation);
        $this->getCurrentTranslation()->shouldReturn($translation);
    }

    function its_fallback_locale_is_mutable()
    {
        $this->setFallbackLocale('en');
        $this->getFallbackLocale()->shouldReturn('en');
    }

    function it_throws_exception_if_no_locale_has_been_set()
    {
        $this->shouldThrow('\RuntimeException')->duringTranslate();
    }

    function it_translates_properly(TranslationInterface $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->setCurrentLocale('en');

        $this->translate()->shouldReturn($translation);
    }

    function it_creates_new_empty_translation_properly()
    {
        $this->setCurrentLocale('en');
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
    protected function getTranslationEntityClass(){
        return  'spec\Sylius\Component\Translation\Model\ConcreteTranslatableTranslation';
    }
}

class ConcreteTranslatableTranslation extends AbstractTranslation
{
}