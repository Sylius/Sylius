<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceFromIdentifierType;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ResourceFromIdentifierTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository, MetadataInterface $metadata)
    {
        $this->beConstructedWith($repository, $metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceFromIdentifierType::class);
    }

    function it_has_a_name(MetadataInterface $metadata)
    {
        $metadata->getName()->willReturn('product');
        $metadata->getApplicationName()->willReturn('sylius');

        $this->getName()->shouldReturn('sylius_product_from_identifier');
    }
}
