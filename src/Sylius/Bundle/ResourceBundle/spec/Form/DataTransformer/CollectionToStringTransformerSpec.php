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

namespace spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CollectionToStringTransformerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(',');
    }

    function it_is_data_transformer(): void
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_collection_to_string(): void
    {
        $this->transform(new ArrayCollection(['abc', 'def', 'ghi', 'jkl']))->shouldReturn('abc,def,ghi,jkl');
    }

    function it_transforms_string_to_collection(): void
    {
        $this->reverseTransform('abc,def,ghi,jkl')->shouldBeLike(new ArrayCollection(['abc', 'def', 'ghi', 'jkl']));
    }

    function it_throws_transformation_failed_exception_if_transform_argument_is_not_a_collection(): void
    {
        $this->shouldThrow(TransformationFailedException::class)->during('transform', [new \stdClass()]);
    }

    function it_throws_transformation_failed_exception_if_transform_argument_is_not_a_string(): void
    {
        $this->shouldThrow(TransformationFailedException::class)->during('reverseTransform', [new \stdClass()]);
    }

    function it_returns_empty_string_if_empty_collection_given(): void
    {
        $this->transform(new ArrayCollection())->shouldReturn('');
    }

    function it_returns_empty_collection_if_empty_string_given(): void
    {
        $this->reverseTransform('')->shouldBeLike(new ArrayCollection());
    }
}
