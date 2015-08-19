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
use Sylius\Component\ImportExport\Model\ExportProfile;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ExportJobSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\ExportJob');
    }

    public function it_extends_job()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\Job');
    }

    public function it_implements_export_job_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Model\ExportJobInterface');
    }

    public function it_has_status()
    {
        $this->setStatus('new');
        $this->getStatus()->shouldReturn('new');
    }

    public function it_has_start_time()
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

    public function it_has_end_time()
    {
        $endTime = new \DateTime('2015-01-01');
        $this->setEndTime($endTime);
        $this->getEndTime()->shouldReturn($endTime);
    }

    public function it_has_created_at()
    {
        $createdAt = new \DateTime('2015-01-01');
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    public function it_has_updated_at()
    {
        $updatedAt = new \DateTime('2015-01-01');
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }

    public function it_has_export_profile(ExportProfile $exportProfile)
    {
        $this->setProfile($exportProfile);
        $this->getProfile()->shouldReturn($exportProfile);
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

    function it_has_file_path()
    {
        $this->setFilePath('/tmp');
        $this->getFilePath()->shouldReturn('/tmp');
    }
}
