<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Association\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Association\Model\AssociationType;
use Sylius\Component\Association\Model\AssociationTypeInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class AssociationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AssociationType::class);
    }

    function it_implements_association_type_interface()
    {
        $this->shouldImplement(AssociationTypeInterface::class);
    }

    function it_has_name()
    {
        $this->setName('Association type name');
        $this->getName()->shouldBe('Association type name');
    }
}
