<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Provider;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class EmailProvider extends AbstractUserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser($email)
    {
        return $this->userRepository->findOneByEmail($email);
    }
}
