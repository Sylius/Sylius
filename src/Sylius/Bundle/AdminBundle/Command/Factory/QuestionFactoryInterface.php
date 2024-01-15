<?php

namespace Sylius\Bundle\AdminBundle\Command\Factory;

use Symfony\Component\Console\Question\Question;

interface QuestionFactoryInterface
{
    public function createEmail(): Question;

    public function createWithNotNullValidator(string $askedQuestion, bool $hidden = false): Question;
}
