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

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class CatalogPromotionNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_catalog_promotion_normalizer_already_called';

    private IriConverterInterface $iriConverter;

    private ProductVariantRepository $productVariantRepository;

    public function __construct(IriConverterInterface $iriConverter, ProductVariantRepository $productVariantRepository)
    {
        $this->iriConverter = $iriConverter;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, CatalogPromotionInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        $rules = [];
        foreach ($data['rules'] as $rule) {
            $rule['configuration'] = $this->replaceCodesWithIriInConfiguration($rule['configuration']);

            $rules[] = $rule;
        }

        $data['rules'] = $rules;

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof CatalogPromotionInterface;
    }

    private function replaceCodesWithIriInConfiguration(array $configuration): array
    {
        $processedConfiguration = [];

        foreach ($configuration as $ruleConfiguration) {
            $variant = $this->productVariantRepository->findOneBy(['code' => $ruleConfiguration]);

            if ($variant === null) {
                throw new ItemNotFoundException();
            }

            $processedConfiguration[] = $this->iriConverter->getIriFromItem($variant);
        }

        return $processedConfiguration;
    }
}
