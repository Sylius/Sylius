<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 02/02/18
 * Time: 10:00
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Addressing\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Generator\PostCodeCodeGeneratorInterface;
use Sylius\Component\Core\Model\AddressInterface;

final class PostCodeCodeGeneratorSpec extends ObjectBehavior
{
    public function it_implements()
    {
        $this->shouldImplement(PostCodeCodeGeneratorInterface::class);
    }

    public function it_generates_correct_code_given_a_valid_country_code_and_postcode()
    {
        $countryCode = 'de';
        $postCode    = '1234';
        $this->generate($countryCode, $postCode)->shouldBeEqualTo('de-1234');
    }

    public function it_throws_an_error_if_country_code_is_not_valid()
    {
        $countryCode = 'abc';
        $postCode    = '1234';

        $this->shouldThrow(\Exception::class)->during('generate', [$countryCode, $postCode]);
    }

    public function it_generates_correct_code_given_an_address(AddressInterface $address)
    {
        $address->getCountryCode()->shouldBeCalled()->willReturn('ch');
        $address->getPostcode()->shouldBeCalled()->willReturn('54567');

        $this->generateFromAddress($address)->shouldBeEqualTo('ch-54567');
    }
}
