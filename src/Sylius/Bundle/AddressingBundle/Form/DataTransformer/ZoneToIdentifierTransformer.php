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
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Zone to id transformer.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ZoneToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * Zone repository.
     *
     * @var ObjectRepository
     */
    private $zoneRepository;

    /**
     * Identifier.
     *
     * @var string
     */
    private $identifier;

    /**
     * Constructor.
     *
     * @param ObjectRepository $zoneRepository
     * @param string           $identifier
     */
    public function __construct(ObjectRepository $zoneRepository, $identifier)
    {
        $this->zoneRepository = $zoneRepository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($zone)
    {
        if (null === $zone) {
            return '';
        }

        if (!$zone instanceof ZoneInterface) {
            throw new UnexpectedTypeException($zone, 'Sylius\Bundle\AddressingBundle\Model\ZoneInterface');
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($zone, $this->identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        return $this->zoneRepository->findOneBy(array($this->identifier => $value));
    }
}
