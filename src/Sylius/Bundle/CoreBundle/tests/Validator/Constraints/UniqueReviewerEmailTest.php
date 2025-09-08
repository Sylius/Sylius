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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmail;
use Symfony\Component\Validator\Constraint;

final class UniqueReviewerEmailTest extends TestCase
{
    private UniqueReviewerEmail $uniqueReviewerEmail;

    protected function setUp(): void
    {
        $this->uniqueReviewerEmail = new UniqueReviewerEmail();
    }

    public function testHasAMessage(): void
    {
        $this->assertSame('sylius.review.author.already_exists', $this->uniqueReviewerEmail->message);
    }

    public function testValidateByUniqueUserEmailValidator(): void
    {
        $this->assertSame('sylius_unique_reviewer_email_validator', $this->uniqueReviewerEmail->validatedBy());
    }

    public function testHasTargets(): void
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->uniqueReviewerEmail->getTargets());
    }
}
