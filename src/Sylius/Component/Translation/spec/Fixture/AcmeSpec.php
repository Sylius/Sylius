<?php

namespace spec\Sylius\Component\Translation\Fixture;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Translation\Fixture\AcmeTranslation;

class AcmeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Translation\Fixture\Acme');
    }

    function it_implements_Sylius_taxonomy_interface()
    {
        $this->shouldImplement('Prezent\Doctrine\Translatable\TranslatableInterface');
    }

    function it_initializes_translattion_collection_by_default()
    {
        $this->getTranslations()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_adds_translation(AcmeTranslation $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->hasTranslation($translation)->shouldReturn(true);
    }

    function it_removes_translation(AcmeTranslation $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();
        $translation->setTranslatable(null)->shouldBeCalled();

        $this->addTranslation($translation);
        $this->removeTranslation($translation);
        $this->hasTranslation($translation)->shouldReturn(false);
    }

    function its_current_locale_is_mutable()
    {
        $this->setCurrentLocale('en');
        $this->getCurrentLocale()->shouldReturn('en');
    }

    function its_current_translation_is_mutable(AcmeTranslation $translation)
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

    function it_translates_properly(AcmeTranslation $translation)
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

        $translation = $this->translate();
        $translation->shouldImplement('Prezent\Doctrine\Translatable\TranslationInterface');
        $translation->acmeProperty->shouldBeNull();
    }

    function it_clones_new_translation_properly(AcmeTranslation $translation)
    {
        $translation->getLocale()->willReturn('en');
        $translation->setTranslatable($this)->shouldBeCalled();
        $translation->acmeProperty = 'acmeProp';

        $this->addTranslation($translation);
        $this->setCurrentLocale('en');
        $translation = $this->translate();
        $translation->shouldImplement('Prezent\Doctrine\Translatable\TranslationInterface');
        $translation->acmeProperty->shouldBe('acmeProp');
    }

    function it_has_fluent_interface(AcmeTranslation $translation)
    {
        $this->setCurrentLocale('en')->shouldReturn($this);
        $this->setCurrentTranslation($translation)->shouldReturn($this);
        $this->setFallbackLocale('en')->shouldReturn($this);
    }
}
