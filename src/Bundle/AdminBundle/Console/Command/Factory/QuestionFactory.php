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

namespace Sylius\Bundle\AdminBundle\Console\Command\Factory;

use Symfony\Component\Console\Question\Question;

final class QuestionFactory implements QuestionFactoryInterface
{
    public function createEmail(): Question
    {
        $question = new Question('Email');
        $question->setValidator(function (?string $email) {
            if ($email === null || !filter_var($email, \FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('The email address provided is invalid. Please try again.');
            }

            return $email;
        });
        $question->setMaxAttempts(3);

        return $question;
    }

    public function createWithNotNullValidator(string $askedQuestion, bool $hidden = false): Question
    {
        $question = new Question($askedQuestion);
        $question->setValidator(function (?string $value) {
            if ($value === null) {
                throw new \InvalidArgumentException('The value cannot be empty.');
            }

            return $value;
        });
        $question->setMaxAttempts(3);
        $question->setHidden($hidden);

        return $question;
    }
}
