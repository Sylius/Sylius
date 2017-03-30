<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class LocalesAwareValidAttributeValueValidator extends ConstraintValidator
{
    /**
     * @var ServiceRegistryInterface
     */
    private $attributeTypeRegistry;

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param ServiceRegistryInterface $attributeTypeRegistry
     */
    public function __construct(ServiceRegistryInterface $attributeTypeRegistry, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->attributeTypeRegistry = $attributeTypeRegistry;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof AttributeValueInterface) {
            throw new UnexpectedTypeException(get_class($value), AttributeValueInterface::class);
        }

        $defaultLocale = $this->localeProvider->getDefaultLocaleCode();
        $configuration = $value->getAttribute()->getConfiguration();

        if ($defaultLocale === $value->getLocaleCode()) {
            $configuration = array_merge($configuration, ['required' => true]);
        }

        /** @var AttributeTypeInterface $attributeType */
        $attributeType = $this->attributeTypeRegistry->get($value->getType());

        $attributeType->validate($value, $this->context, $configuration);
    }
}
