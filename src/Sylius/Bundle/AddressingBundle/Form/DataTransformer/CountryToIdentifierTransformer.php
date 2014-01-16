<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\AddressingBundle\Model\CountryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Country to id transformer.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CountryToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * Country repository.
     *
     * @var ObjectRepository
     */
    private $countryRepository;

    /**
     * Identifier.
     *
     * @var string
     */
    private $identifier;

    /**
     * Constructor.
     *
     * @param ObjectRepository $countryRepository
     * @param string           $identifier
     */
    public function __construct(ObjectRepository $countryRepository, $identifier)
    {
        $this->countryRepository = $countryRepository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        return $this->countryRepository->findOneBy(array($this->identifier => $value));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($country)
    {
        if (null === $country) {
            return '';
        }

        if (!$country instanceof CountryInterface) {
            throw new UnexpectedTypeException($country, 'Sylius\Bundle\AddressingBundle\Model\CountryInterface');
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($country, $this->identifier);
    }
}
