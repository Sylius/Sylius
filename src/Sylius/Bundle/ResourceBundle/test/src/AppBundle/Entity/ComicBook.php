<?php

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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAuthorFirstName()
    {
        return $this->authorFirstName;
    }

    /**
     * @param string $authorFirstName
     */
    public function setAuthorFirstName($authorFirstName)
    {
        $this->authorFirstName = $authorFirstName;
    }

    /**
     * @return string
     */
    public function getAuthorLastName()
    {
        return $this->authorLastName;
    }

    /**
     * @param string $authorLastName
     */
    public function setAuthorLastName($authorLastName)
    {
        $this->authorLastName = $authorLastName;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return sprintf('%s %s', $this->authorFirstName, $this->authorLastName);
    }
}
