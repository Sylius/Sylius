<?php

namespace spec\Sylius\Component\Translation\Fixture;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Translation\Fixture\Acme;
use Sylius\Component\Translation\Fixture\AcmeTranslation;

class AcmeTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Translation\Fixture\AcmeTranslation');
    }

    function it_implements_Sylius_taxonomy_interface()
    {
        $this->shouldImplement('Prezent\Doctrine\Translatable\TranslationInterface');
    }

    function its_sets_its_translatable_correctly(Acme $translatable)
    {
        $this->setTranslatable($translatable);
        $this->getTranslatable()->shouldReturn($translatable);
    }

    function its_detaches_from_its_translatable_correctly()
    {
        $translatable1 = new Acme();
        $translatable2 = new Acme();

        $translation = new AcmeTranslation();

        $translation->setTranslatable($translatable1);
        $translation->setTranslatable($translatable2);

        \PHPUnit_Framework_Assert::assertEquals(false, $translatable1->hasTranslation($translation));
        \PHPUnit_Framework_Assert::assertEquals($translatable2, $translation->getTranslatable());
    }

    function its_locale_is_mutable(AcmeTranslation $translation)
    {
        $this->setLocale('en');
        $this->getLocale()->shouldReturn('en');
    }

    function it_has_fluent_interface(AcmeTranslation $translation)
    {
        $this->setTranslatable()->shouldReturn($this);
        $this->setLocale('en')->shouldReturn($this);
    }
}
