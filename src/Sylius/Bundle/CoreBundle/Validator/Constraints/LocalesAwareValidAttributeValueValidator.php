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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

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
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AttributeValueInterface::class);

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
