<?php

namespace Sylius\Bundle\ReviewBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

interface ReviewInterface extends TimestampableInterface
{
    const MODERATION_STATUS_UNMODERATED = 'new';
    const MODERATION_STATUS_APPROVED = 'approved';
    const MODERATION_STATUS_REJECTED = 'rejected';

    /**
     * @param string $title
     * @return ReviewInterface
     */
    public function setTitle($title);

    /**
     * @return string 
     */
    public function getTitle();

    /**
     * @param integer $rating
     * @return ReviewInterface
     */
    public function setRating($rating);

    /**
     * @return integer 
     */
    public function getRating();

    /**
     * @param string $comment
     * @return ReviewInterface
     */
    public function setComment($comment);

    /**
     * @return string 
     */
    public function getComment();

    /**
     * @param string $moderationStatus
     * @return ReviewInterface
     */
    public function setModerationStatus($moderationStatus);

    /**
     * @return string 
     */
    public function getModerationStatus();

    /**
     * Set guestReviewer.
     *
     * @param GuestReviewerInterface $guestReviewer
     */
    public function setGuestReviewer(GuestReviewerInterface $guestReviewer);

    /**
     * Get guestReviewer.
     *
     * @return GuestReviewerInterface
     */
    public function getGuestReviewer();
}