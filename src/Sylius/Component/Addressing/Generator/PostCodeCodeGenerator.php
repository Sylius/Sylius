<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 02/02/18
 * Time: 09:59
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Generator;


use InvalidArgumentException;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\PostalCodeInterface;

class PostCodeCodeGenerator implements PostCodeCodeGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(string $countryCode, string $postCode): string
    {
        if (strlen($countryCode) !== 2) {
            throw new InvalidArgumentException('Country code has to be the ISO code and 2 characters long');
        }

        return sprintf('%s-%s', $countryCode, $postCode);
    }

    public function generateFromPostCode(PostalCodeInterface $postalCode): string
    {
        return $this->generate($postalCode->getCountry()->getCode(), $postalCode->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromAddress(AddressInterface $address): string
    {
        return $this->generate($address->getCountryCode(), $address->getPostcode());
    }
}