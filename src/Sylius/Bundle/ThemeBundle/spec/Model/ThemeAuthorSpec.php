<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeAuthorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeAuthor::class);
    }

    function it_implements_theme_author_interface()
    {
        $this->shouldImplement(ThemeAuthor::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn(null);

        $this->setName('Krzysztof Krawczyk');
        $this->getName()->shouldReturn('Krzysztof Krawczyk');
    }

    function it_has_email()
    {
        $this->getEmail()->shouldReturn(null);

        $this->setEmail('cristopher@example.com');
        $this->getEmail()->shouldReturn('cristopher@example.com');
    }

    function it_has_homepage()
    {
        $this->getHomepage()->shouldReturn(null);

        $this->setHomepage('http://example.com');
        $this->getHomepage()->shouldReturn('http://example.com');
    }

    function it_has_role()
    {
        $this->getRole()->shouldReturn(null);

        $this->setRole('Developer');
        $this->getRole()->shouldReturn('Developer');
    }
}
