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

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

final class ReadOperationContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct (
        private SerializerContextBuilderInterface $decorated,
//        We can allow configuring these two options to allow smoother transition from :read to :index and :show
//        private bool $skipAddingReadGroup,
//        private bool $skipAddingIndexAndShowGroups,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        $groups = $context['groups'] ?? [];
        $groups = is_string($groups) ? [$groups] : $groups;

        foreach ($groups as $group) {
            if ($this->shouldReadGroupBeAdded($group)) {
                $readGroup = str_replace([':show', ':index'], ':read', $group);

                if (in_array($readGroup, $groups, true)) {
                    continue;
                }

                $groups[] = $readGroup;
            }

            if ($this->shouldIndexAndShowGroupsBeAdded($group)) {
                $indexGroup = str_replace(':read', ':index', $group);
                $showGroup = str_replace(':read', ':show', $group);

                if (!in_array($indexGroup, $groups, true)) {
                    $groups[] = $indexGroup;
                }

                if (!in_array($showGroup, $groups, true)) {
                    $groups[] = $showGroup;
                }
            }
        }

        $context['groups'] = $groups;

        return $context;
    }

    private function shouldReadGroupBeAdded(string $group): bool
    {
        return str_ends_with($group, ':show') || str_ends_with($group, ':index');
    }

    private function shouldIndexAndShowGroupsBeAdded(string $group): bool
    {
        return str_ends_with($group, ':read');
    }
}
