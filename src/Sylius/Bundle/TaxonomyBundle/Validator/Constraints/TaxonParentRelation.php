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

namespace Sylius\Bundle\TaxonomyBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class TaxonParentRelation extends Constraint
{

    #[HasNamedArguments]
    public function __construct(
        string $message = 'sylius.taxon.parent.invalid_relation',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->message = $message;
    }

    public string $message = 'sylius.taxon.parent.invalid_relation';

    public function validatedBy(): string
    {
        return 'sylius_taxon_parent_relation_validator';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
