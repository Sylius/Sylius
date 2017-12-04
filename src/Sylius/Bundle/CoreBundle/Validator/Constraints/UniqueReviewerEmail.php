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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueReviewerEmail extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.review.author.already_exists';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'sylius_unique_reviewer_email_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
