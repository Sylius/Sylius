<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Taxonomy\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonTranslationSpec extends ObjectBehavior
{
    public function it_implements_taxon_translation_interface(): void
    {
        $this->shouldImplement(TaxonTranslationInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable(): void
    {
        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    public function it_returns_name_when_converted_to_string(): void
    {
        $this->setName('Brand');
        $this->__toString()->shouldReturn('Brand');
    }

    public function it_has_no_description_by_default(): void
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable(): void
    {
        $this->setDescription('This is a list of brands.');
        $this->getDescription()->shouldReturn('This is a list of brands.');
    }

    public function it_has_no_slug_by_default(): void
    {
        $this->getSlug()->shouldReturn(null);
    }

    public function its_slug_is_mutable(): void
    {
        $this->setSlug('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }
}
