<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Metadata\Model\Custom;

use Sylius\Metadata\Model\MetadataInterface;
use Sylius\Metadata\Model\Twitter\CardInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface PageMetadataInterface extends MetadataInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string[]
     */
    public function getKeywords();

    /**
     * @param string[] $keywords
     */
    public function setKeywords(array $keywords);

    /**
     * @return string
     */
    public function getCharset();

    /**
     * @param string $charset
     */
    public function setCharset($charset);

    /**
     * @return CardInterface|null
     */
    public function getTwitter();

    /**
     * @param CardInterface|null $card
     */
    public function setTwitter(CardInterface $card = null);
}
