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

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class ComicBook implements ResourceInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $authorFirstName;

    /**
     * @var string
     */
    private $authorLastName;

    /**
     * @var string
     */
    private $title;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAuthorFirstName(): string
    {
        return $this->authorFirstName;
    }

    /**
     * @param string $authorFirstName
     */
    public function setAuthorFirstName(string $authorFirstName): void
    {
        $this->authorFirstName = $authorFirstName;
    }

    /**
     * @return string
     */
    public function getAuthorLastName(): string
    {
        return $this->authorLastName;
    }

    /**
     * @param string $authorLastName
     */
    public function setAuthorLastName(string $authorLastName): void
    {
        $this->authorLastName = $authorLastName;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return sprintf('%s %s', $this->authorFirstName, $this->authorLastName);
    }
}
