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

namespace Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class ZoneDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_zone_denormalizer_already_called';

    public function __construct(private IriConverterInterface $iriConverter)
    {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        if (
            isset($context[self::ALREADY_CALLED]) ||
            !is_object($context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null) ||
            !$context[AbstractNormalizer::OBJECT_TO_POPULATE] instanceof ZoneInterface
        ) {
            return false;
        }

        return is_a($type, ZoneInterface::class, true);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        /** @var ZoneInterface $zone */
        $zone = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        Assert::isInstanceOf($zone, ZoneInterface::class);

        /** @var ArrayCollection $members */
        $members = $zone->getMembers();
        Assert::isInstanceOf($members, Selectable::class);

        $membersCodes = $members->map(fn ($member): string => $member->getCode())->getValues();

        foreach ($data['members'] ?? [] as $key => $member) {
            if (isset($member['code']) && in_array($member['code'], $membersCodes)) {
                unset($data['members'][$key]);

                $data['members'][$key] = $this->iriConverter->getIriFromResource(
                    $this->getZoneMemberByCode($members, $member['code']),
                );
            }
        }

        if (isset($data['members'])) {
            $data['members'] = array_values($data['members']);
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    private function getZoneMemberByCode(Selectable $zoneMembers, string $code): ZoneMemberInterface
    {
        return $zoneMembers->matching(Criteria::create()->where(Criteria::expr()->eq('code', $code)))->first();
    }
}
