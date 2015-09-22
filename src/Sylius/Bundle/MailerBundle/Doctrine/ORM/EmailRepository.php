<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\Doctrine\ORM;

use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Mailer\Repository\EmailRepositoryInterface;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
class EmailRepository extends TranslatableResourceRepository implements EmailRepositoryInterface
{
}
