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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use ApiPlatform\Core\Api\IriConverterInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/** @experimental */
final class LocaleCodeAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(LocaleContextInterface $localeContext, IriConverterInterface $iriConverter)
    {
        $this->localeContext = $localeContext;
        $this->iriConverter = $iriConverter;
    }

    public function transform($object, string $to, array $context = [])
    {
        if ($object->getLocale() !== null) {
            /** @var LocaleInterface $locale */
            $locale = $this->iriConverter->getItemFromIri($object->getLocale());
            $object->setLocaleCode($locale->getCode());

            return $object;
        }

        $localeCode = $this->localeContext->getLocaleCode();

        $object->setLocaleCode($localeCode);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof LocaleCodeAwareInterface;
    }
}
