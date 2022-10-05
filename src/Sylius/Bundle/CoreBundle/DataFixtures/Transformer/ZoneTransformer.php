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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneMemberFactory;

final class ZoneTransformer implements ZoneTransformerInterface
{
    use TransformNameToCodeAttributeTrait;

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);

        $members = [];

        foreach ($attributes['members'] as $member) {
            if (\is_string($member)) {
                $member = ZoneMemberFactory::findOrCreate(['code' => $member]);
            }

            $members[] = $member;
        }

        $attributes['members'] = $members;

        return $attributes;
    }
}
