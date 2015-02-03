<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * ImportExportContext for ImportExport scenarios
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ImportExportContext extends DefaultContext
{
    /**
     * @Given there are following export profiles configured:
     * @And there are following export profiles configured:
     */
    public function thereAreExportProfiles(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('export_profile');

        foreach ($table->getHash() as $data) {
            $this->thereIsExportProfile($data['name'], $data['description'], $data["code"], $data['reader'], $data['reader configuration'], $data['writer'], $data['writer configuration'], false);
        }

        $manager->flush();
    }
    
    public function thereIsExportProfile($name, $description, $code, $reader, $readerConfiguration, $writer, $writerConfiguration, $flush = true)
    {
        $repository = $this->getRepository('export_profile');
        $exportProfile = $repository->createNew();
        $exportProfile->setName($name);
        $exportProfile->setDescription($description);
        $exportProfile->setCode($code);
        
        $exportProfile->setReader($reader);
        $exportProfile->setReaderConfiguration($this->getConfiguration($readerConfiguration));

        $writerConfiguration = $this->getConfiguration($writerConfiguration);
        $writerConfiguration["add_headers"] = isset($writerConfiguration["add_headers"]) ? false : true;

        $exportProfile->setWriter($writer);
        $exportProfile->setWriterConfiguration($writerConfiguration);

        $manager = $this->getEntityManager();
        $manager->persist($exportProfile);

        if ($flush) {
            $manager->flush();
        }
        
        return $exportProfile;
    }

    /**
     * @Given there are following export jobs set:
     * @And there are following export jobs set:
     */
    public function thereAreExportJobs(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $this->thereIsExportJob($data['status'], $data['startTime'], $data["endTime"], $data['createdAt'], $data['updatedAt'], $data["exportProfileCode"], false);
        }

        $manager->flush();
    }
    
    public function thereIsExportJob($status, $startTime, $endTime, $createdAt, $updatedAt, $exportProfileCode, $flush = true)
    {
        $repository = $this->getRepository('export_job');
        $exportJob = $repository->createNew();
        $exportJob->setStatus($status);
        $exportJob->setStartTime(new \DateTime($startTime));
        $exportJob->setEndTime(new \DateTime($endTime));
        $exportJob->setCreatedAt(new \DateTime($createdAt));
        $exportJob->setUpdatedAt(new \DateTime($updatedAt));
        
        $exportProfile = $this->getRepository('export_profile')->findOneByCode($exportProfileCode);
        $exportJob->setExportProfile($exportProfile);

        $manager = $this->getEntityManager();
        $manager->persist($exportJob);

        if ($flush) {
            $manager->flush();
        }
        
        return $exportJob;
    }

    /**
     * @Given I am on the export jobs index page for profile with code :code
     * @Then I should be on the export jobs index page for profile with code :code
     */
    public function iShouldBeOnTheExportJobsIndexPageForProfileWithCode($code)
    {
        $exportProfile = $this->findOneBy('export_profile', array('code' => $code));

        $this->getSession()->visit($this->generatePageUrl('sylius_backend_export_job_index', array('profileId' => $exportProfile->getId())));
    }

}
