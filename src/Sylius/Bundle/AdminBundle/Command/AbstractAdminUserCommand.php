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

namespace Sylius\Bundle\AdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractAdminUserCommand extends Command
{
    protected SymfonyStyle $io;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function createEmailQuestion(): Question
    {
        $question = new Question('Email');
        $question->setValidator(function (?string $email) {
            if (!filter_var($email, \FILTER_VALIDATE_EMAIL) || $email === null) {
                throw new \InvalidArgumentException('The email address provided is invalid. Please try again.');
            }

            return $email;
        });
        $question->setMaxAttempts(3);

        return $question;
    }

    protected function createQuestionWithNonBlankValidator(string $askedQuestion, bool $hidden = false): Question
    {
        $question = new Question($askedQuestion);
        $question->setValidator(function (?string $value) {
            if ($value === null) {
                throw new \InvalidArgumentException('The value cannot be empty.');
            }

            return $value;
        });
        $question->setMaxAttempts(3);

        if ($hidden) {
            $question->setHidden(true);
        }

        return $question;
    }
}
