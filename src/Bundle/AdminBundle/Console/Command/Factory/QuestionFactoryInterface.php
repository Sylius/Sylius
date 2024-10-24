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

interface QuestionFactoryInterface
{
    public function createEmail(): Question;

    public function createWithNotNullValidator(string $askedQuestion, bool $hidden = false): Question;
}
