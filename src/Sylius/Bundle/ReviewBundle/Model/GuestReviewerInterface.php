<?php

namespace Sylius\Bundle\ReviewBundle\Model;

interface GuestReviewerInterface 
{
	/**
     * @param string $name
     * @return GuestReviewInterface
     */
	public function setName($name);

    /**
     * @return string
     */
	public function getName();

    /**
     * @param string $email
     * @return ReviewInterface
     */
	public function setEmail($email);

    /**
     * @return string
     */
	public function getEmail();

    /**
     * @param \DateTime $createdAt
     * @return ReviewInterface
     */
	public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return datetime
     */
	public function getCreatedAt();

    /**
     * @param \DateTime $updatedAt
     * @return ReviewInterface
     */
	public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return datetime
     */
	public function getUpdatedAt();
}