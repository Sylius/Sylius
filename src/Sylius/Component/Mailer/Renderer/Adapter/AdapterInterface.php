<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Renderer\Adapter;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

/**
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
interface AdapterInterface
{
    /**
     * @param EmailInterface $email
     * @param array $data
     *
     * @return RenderedEmail
     */
    public function render(EmailInterface $email, array $data = []);
}
