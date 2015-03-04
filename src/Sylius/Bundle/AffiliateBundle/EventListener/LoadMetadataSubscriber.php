<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LoadMetadataSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    protected $affiliate;

    /**
     * @var string
     */
    protected $referral;

    public function __construct($affiliate, $referral)
    {
        $this->affiliate = $affiliate;
        $this->referral = $referral;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($this->affiliate === $metadata->getName()) {
            $this->mapReferrals($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo|ClassMetadata $metadata
     */
    private function mapReferrals(ClassMetadataInfo $metadata)
    {
        $metadata->mapOneToMany(array(
            'fieldName'    => 'referrals',
            'type'         => ClassMetadataInfo::ONE_TO_MANY,
            'targetEntity' => $this->referral,
            'cascade'      => array('all'),
            'mappedBy'     => 'referrer',
            'joinTable'    => array(
                'name' => 'sylius_referrals',
                'joinColumns' => array(array(
                    'name'                 => 'referral_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'unique'               => false,
                )),
                'inverseJoinColumns' => array(array(
                    'name'                 => 'affiliate_id',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                    'unique'               => false,
                ))
            ),
        ));
    }
}
