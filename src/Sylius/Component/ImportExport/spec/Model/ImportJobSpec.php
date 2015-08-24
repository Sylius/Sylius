<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\ImportExport\Model\ImportProfile;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportJobSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\ImportJob');
    }

    function it_extends_job()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\Job');
    }

    function it_implements_import_job_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Model\ImportJobInterface');
    }

    function it_has_status()
    {
        $this->setStatus('new');
        $this->getStatus()->shouldReturn('new');
    }

    function it_has_start_time()
    {
        $startTime = new \DateTime('2015-01-01');
        $this->setStartTime($startTime);
        $this->getStartTime()->shouldReturn($startTime);
    }

    public function it_has_created_at_and_updated_at_from_beginning()
    {
        $dateTime = new \DateTime('now');
        $this->getCreatedAt()->shouldBeLike($dateTime);
        $this->getUpdatedAt()->shouldBeLike($dateTime);
    }

    function it_has_end_time()
    {
        $endTime = new \DateTime('2015-01-01');
        $this->setEndTime($endTime);
        $this->getEndTime()->shouldReturn($endTime);
    }

    function it_has_created_at()
    {
        $createdAt = new \DateTime('2015-01-01');
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function it_has_updated_at()
    {
        $updatedAt = new \DateTime('2015-01-01');
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }

    function it_has_import_profile(ImportProfile $importProfile)
    {
        $this->setProfile($importProfile);
        $this->getProfile()->shouldReturn($importProfile);
    }
    
    function it_has_metadata()
    {
        $this->setMetadata(array());
        $this->getMetadata()->shouldReturn(array());
    }

    function it_adds_metadata()
    {
        $this->setMetadata(array('old'));
        $this->addMetadata(array('new'));
        $this->getMetadata()->shouldReturn(array('old', 'new'));
    }
}
