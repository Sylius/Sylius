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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class AddressDenormalizer extends ObjectNormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface, ContextAwareNormalizerInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    /** @var string */
    private $classType;

    /** @var string */
    private $interfaceType;

    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        callable $objectClassResolver = null,
        array $defaultContext = [],
        string $classType = null,
        string $interfaceType = null
    ) {
        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyAccessor,
            $propertyTypeExtractor,
            $classDiscriminatorResolver,
            $objectClassResolver,
            $defaultContext
        );

        $this->classType = $classType;
        $this->interfaceType = $interfaceType;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return parent::denormalize(
            $data,
            $this->classType,
            $format,
            $context
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return $type === $this->interfaceType;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return false;
    }
}
