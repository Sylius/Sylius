<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Review\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ReviewInterface extends TimestampableInterface, ResourceInterface
{
    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param int $rating
     */
    public function setRating($rating);

    /**
     * @return int
     */
    public function getRating();

    /**
     * @param string $comment
     */
    public function setComment($comment);

    /**
     * @return string
     */
    public function getComment();

    /**
     * @param ReviewerInterface $author
     */
    public function setAuthor(ReviewerInterface $author = null);

    /**
     * @return ReviewerInterface
     */
    public function getAuthor();

    /**
     * @param string $status
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return ReviewableInterface
     */
    public function getReviewSubject();

    /**
     * @param ReviewableInterface $reviewSubject
     */
    public function setReviewSubject(ReviewableInterface $reviewSubject);
}
