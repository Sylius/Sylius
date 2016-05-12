<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Like\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
interface LikeInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return boolean
     */
    public function isAuthorLike();

    /**
     * @param boolean $authorlike
     */
    public function setAuthorLike($authorlike);
    
    /**
     * @param LikerInterface $author
     */
    public function setAuthor(LikerInterface $author = null);

    /**
     * @return LikerInterface
     */
    public function getAuthor();

    /**
     * @return LikableInterface
     */
    public function getLikeSubject();

    /**
     * @param LikableInterface $likeSubject
     */
    public function setLikeSubject(LikableInterface $likeSubject);
}
