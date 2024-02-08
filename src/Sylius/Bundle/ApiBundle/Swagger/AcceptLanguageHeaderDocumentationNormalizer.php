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

namespace Sylius\Bundle\ApiBundle\Swagger;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class AcceptLanguageHeaderDocumentationNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $decoratedNormalizer,
        private RepositoryInterface $localeRepository,
    ) {
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $acceptLanguageHeader = [
            'name' => 'Accept-Language',
            'in' => 'header',
            'required' => false,
            'description' => 'Locales in this enum are all locales defined in the shop and only enabled ones will work in the given channel in the shop.',
            'schema' => [
                'type' => 'string',
                'enum' => array_map(
                    fn (LocaleInterface $locale): string => $locale->getCode(),
                    $this->localeRepository->findAll(),
                ),
            ],
        ];

        foreach ($docs['paths'] as $path => $methods) {
            foreach ($methods as $methodName => $methodBody) {
                if (is_object($methodBody)) {
                    $methodBody = $methodBody->getArrayCopy();
                    $methodBody['parameters'][] = $acceptLanguageHeader;

                    $docs['paths'][$path][$methodName] = $methodBody;
                }
            }
        }

        return $docs;
    }
}
