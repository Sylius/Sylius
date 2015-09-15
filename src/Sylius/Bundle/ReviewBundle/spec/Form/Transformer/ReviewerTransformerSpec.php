<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Form\Transformer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewerTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Form\Transformer\ReviewerTransformer');
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement('Symfony\Component\Form\DataTransformerInterface');
    }

    function it_transforms_form_data(ReviewerInterface $author)
    {
        $author->getEmail()->willReturn('john.doe@example.com');

        $this->transform($author)->shouldReturn('john.doe@example.com');
    }

    function it_returns_null_if_given_value_is_null()
    {
        $this->transform(null)->shouldReturn(null);
    }

    function it_throws_exception_if_given_value_is_not_customer_interface_object()
    {
        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Review\Model\ReviewerInterface'))->during('transform', array('badObject'));
    }

    function it_reverse_transforms_form_data(ReviewerInterface $author)
    {
        $author->getEmail()->willReturn('john.doe@example.com');

        $this->reverseTransform('john.doe@example.com')->shouldBeSameAs($author);
    }

    function it_returns_null_if_given_value_is_incorrect(\DateTime $wrongObject)
    {
        $this->reverseTransform(null)->shouldReturn(null);
        $this->reverseTransform($wrongObject)->shouldReturn(null);
    }

    public function getMatchers()
    {
        return array(
            'beSameAs' => function ($subject, $key) {
                if (!$subject instanceof ReviewerInterface || !$key instanceof ReviewerInterface) {
                    return false;
                }

                return $subject->getEmail() === $key->getEmail();
            },
        );
    }
}
