<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeAuthorFactory implements ThemeAuthorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data)
    {
        /** @var ThemeAuthor $author */
        $author = new ThemeAuthor();

        $author->setName(isset($data['name']) ? $data['name'] : null);
        $author->setEmail(isset($data['email']) ? $data['email'] : null);
        $author->setHomepage(isset($data['homepage']) ? $data['homepage'] : null);
        $author->setRole(isset($data['role']) ? $data['role'] : null);

        return $author;
    }
}
