<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 02/02/18
 * Time: 10:28
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Generator;


use Sylius\Component\Addressing\Model\AddressInterface;

interface PostCodeCodeGeneratorInterface
{
    /**
     * Generates the postcode-code from the country code and the postcode
     *
     * @param string $countryCode
     * @param string $postalCode
     *
     * @return string
     */
    public function generate(string $countryCode, string $postCode): string;

    /**
     * Generates the postcode-code
     *
     * @param AddressInterface $address
     *
     * @return string
     */
    public function generateFromAddress(AddressInterface $address): string;
}