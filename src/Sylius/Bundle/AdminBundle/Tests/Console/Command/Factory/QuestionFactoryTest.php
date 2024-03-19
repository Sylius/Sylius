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

namespace Sylius\Bundle\AdminBundle\Tests\Console\Command\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactory;

final class QuestionFactoryTest extends TestCase
{
    /** @test */
    public function it_creates_email_question(): void
    {
        $questionFactory = new QuestionFactory();
        $question = $questionFactory->createEmail();

        $this->assertSame('Email', $question->getQuestion());
        $this->assertSame(3, $question->getMaxAttempts());
        $this->assertEquals('test@example.com', $question->getValidator()('test@example.com'));
    }

    /** @test */
    public function it_creates_email_question_with_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The email address provided is invalid. Please try again.');

        $questionFactory = new QuestionFactory();
        $emailQuestion = $questionFactory->createEmail();

        $emailQuestion->getValidator()('invalid-email');
    }

    /** @test */
    public function it_creates_email_question_with_null_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The email address provided is invalid. Please try again.');

        $questionFactory = new QuestionFactory();
        $emailQuestion = $questionFactory->createEmail();

        $emailQuestion->getValidator()(null);
    }

    /** @test */
    public function it_creates_question_with_not_null_validator(): void
    {
        $questionFactory = new QuestionFactory();
        $question = $questionFactory->createWithNotNullValidator('Question');

        $this->assertSame('Question', $question->getQuestion());
        $this->assertSame(3, $question->getMaxAttempts());
        $this->assertEquals('test', $question->getValidator()('test'));
    }

    /** @test */
    public function it_creates_question_with_not_null_validator_with_null_value(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value cannot be empty.');

        $questionFactory = new QuestionFactory();
        $question = $questionFactory->createWithNotNullValidator('Question');

        $question->getValidator()(null);
    }
}
