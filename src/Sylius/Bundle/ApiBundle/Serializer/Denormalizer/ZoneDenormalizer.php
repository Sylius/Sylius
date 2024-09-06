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

namespace Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final readonly class ZoneDenormalizer implements DenormalizerInterface
{
    private const ALREADY_CALLED = 'sylius_zone_denormalizer_already_called';

    public function __construct(
        private DenormalizerInterface $denormalizer,
        private SectionProviderInterface $sectionProvider,
    ) {
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        Assert::keyNotExists($context, self::ALREADY_CALLED);
        Assert::isInstanceOf($this->sectionProvider->getSection(), AdminApiSection::class);
        Assert::isInstanceOf($context[AbstractNormalizer::OBJECT_TO_POPULATE], ZoneInterface::class);

        $context[self::ALREADY_CALLED] = true;

        $currentZoneMembers = [];
        foreach ($context[AbstractNormalizer::OBJECT_TO_POPULATE]->getMembers() as $member) {
            $currentZoneMembers[$member->getCode()] = $member;
        }

        $zone = $this->denormalizer->denormalize($data, $type, $format, $context);

        foreach ($zone->getMembers() as $member) {
            if (isset($currentZoneMembers[$member->getCode()])) {
                $zone->removeMember($member);
                $zone->addMember($currentZoneMembers[$member->getCode()]);
            }
        }

        return $zone;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) &&
            $this->sectionProvider->getSection() instanceof AdminApiSection &&
            ($context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null) instanceof ZoneInterface &&
            is_a($type, ZoneInterface::class, true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ZoneInterface::class => true];
    }
}
