<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    public function __construct(
        private ServiceRegistryInterface $attributeTypeRegistry,
        private TranslationLocaleProviderInterface $localeProvider,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
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
