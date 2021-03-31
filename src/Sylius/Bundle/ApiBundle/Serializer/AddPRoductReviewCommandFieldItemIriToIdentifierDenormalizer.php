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
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class AddPRoductReviewCommandFieldItemIriToIdentifierDenormalizer implements ContextAwareDenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $objectNormalizer;

    /** @var CommandAwareInputDataTransformer */
    private $commandAwareInputDataTransformer;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        DenormalizerInterface $objectNormalizer,
        CommandAwareInputDataTransformer $commandAwareInputDataTransformer,
        IriConverterInterface $iriConverter
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->commandAwareInputDataTransformer = $commandAwareInputDataTransformer;
        $this->iriConverter = $iriConverter;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return $this->getInputClassName($context) === AddProductReview::class ? true : false;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @var ProductInterface $product */
        $product = $this->iriConverter->getItemFromIri($data['product']);

        $data['product'] = $product->getCode();

        $denormalizedInput = $this->objectNormalizer->denormalize($data, $this->getInputClassName($context), $format, $context);

        if ($this->commandAwareInputDataTransformer->supportsTransformation($denormalizedInput, $type, $context)) {
            return $this->commandAwareInputDataTransformer->transform($denormalizedInput, $type, $context);
        }

        return $denormalizedInput;
    }

    private function getInputClassName(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }
}
