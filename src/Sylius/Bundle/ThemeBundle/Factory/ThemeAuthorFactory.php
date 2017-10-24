<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Factory;

use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;

final class ThemeAuthorFactory implements ThemeAuthorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data): ThemeAuthor
    {
        /** @var ThemeAuthor $author */
        $author = new ThemeAuthor();

        $author->setName($data['name'] ?? null);
        $author->setEmail($data['email'] ?? null);
        $author->setHomepage($data['homepage'] ?? null);
        $author->setRole($data['role'] ?? null);

        return $author;
    }
}
