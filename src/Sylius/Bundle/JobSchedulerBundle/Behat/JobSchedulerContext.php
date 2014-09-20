<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Symfony\Component\Locale\Locale;
use Sylius\Bundle\JobSchedulerBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Sylius\Bundle\JobSchedulerBundle\Command\RunActiveJobsCommand;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Testwork\Tester\Result\TestResult;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobStatus;
use Behat\Mink\Driver\Selenium2Driver;


require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Feature context.
 */
class JobSchedulerContext extends DefaultContext
{
    private $application;
    private $tester;
    private $em;

    /**
     * @BeforeScenario
     */
    public function setApp()
    {
        $this->application = new Application($this->kernel);
        $this->application->add(new RunActiveJobsCommand());
        $this->em = $this->kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @When /^I run "([^"]*)" command$/
     */
    public function iRunCommand($arg1)
    {
        $command      = $this->application->find($arg1);
        $this->tester = new CommandTester($command);
        $this->tester->execute(array('command' => $command->getName()));
    }

    /**
     * @Given /^I have a job with command "([^"]*)" and schedule "([^"]*)" that should run on "([^"]*)" server type$/
     */
    public function iHaveAJobWithCommandAndScheduleThatShouldRunOnServerType($command, $schedule, $serverType)
    {
        $this->thereIsJob($command, $schedule);
        $job = $this->getJob($command);
        $job->setServerType($serverType);
        $this->em->persist($job);
        $this->em->flush();

    }

    /**
     * @When /^I run "([^"]*)" command on a "([^"]*)" server type$/
     */
    public function iRunCommandOnAServerType($command, $serverType)
    {
        putenv('ST=' . $serverType);
        $this->iRunCommand($command);

    }

    /**
     * @Given /^I have a job with command "([^"]*)" and schedule "([^"]*)" that should run on "([^"]*)" environment$/
     */
    public function iHaveAJobWithCommandAndScheduleThatShouldRunOnEnvironment($command, $schedule, $environment)
    {
        $this->thereIsJob($command, $schedule);
        $job = $this->getJob($command);
        $job->setEnvironment($environment);
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * @When /^I run "([^"]*)" command on a "([^"]*)" environment$/
     */
    public function iRunCommandOnAEnvironment($command, $environment)
    {
        putenv('ENV=' . $environment);
        $this->iRunCommand($command);
    }

    /**
     * @Then /^The last log of "([^"]*)" command should contain$/
     */
    public function theLastLogOfCommandShouldContain($command, $string)
    {
        $output = $this->getOutput($command);
        assertSame($string->getRaw(), $output);
    }

    /**
     * @Then /^The last log of "([^"]*)" command should not contain$/
     */
    public function theLastLogOfCommandShouldNotContain($command, $string)
    {
        $output = $this->getOutput($command);

        assertNotSame($string->getRaw(), $output);
    }

    /**
     * @Given /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Then /^There should be "([^"]*)" logs of command "([^"]*)"$/
     */
    public function thereShouldBeLogsOfCommand($numLogs, $command)
    {
        $logs = $this->logsOfCommand($command, JobStatus::SUCCESS);

        assertSame(count($logs), (int)$numLogs);
    }

    /**
     * @Given /^Settings are "([^"]*)"$/
     */
    public function iHaveSettings($arg1)
    {
        $active = false;
        if ($arg1 == 'enabled') {
            $active = true;
        }
        $settings = $this->kernel->getContainer()->get('sylius.settings.manager')->loadSettings('job_scheduler');
        $settings->set('enabled', $active);

        $this->kernel->getContainer()->get('sylius.settings.manager')->saveSettings('job_scheduler', $settings);
    }

    /**
     * @Given /^I have a job with command "([^"]*)" and schedule "([^"]*)" and priority "([^"]*)"$/
     */
    public function iHaveAJobWithCommandAndScheduleAndPriority($command, $schedule, $priority)
    {
        $this->thereIsJob($command, $schedule);
        $job = $this->getJob($command);
        $job->setPriority($priority);
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * @Then /^"([^"]*)" should have been executed before "([^"]*)"$/
     */
    public function shouldHaveBeenExecutedBefore($command1, $command2)
    {
        $job  = $this->getJob($command1);
        $logs = $this->em->getRepository('SyliusJobSchedulerBundle:JobLog')->findBy(array('job' => $job->getId()));
        if (array_key_exists(0, $logs)) {
            $exec1 = $logs[0]->getStartedAt();
        }

        $job  = $this->getJob($command2);
        $logs = $this->em->getRepository('SyliusJobSchedulerBundle:JobLog')->findBy(array('job' => $job->getId()));
        if (array_key_exists(0, $logs)) {
            $exec2 = $logs[0]->getStartedAt();
        }

        assertGreaterThan(0, $exec2 - $exec1);
    }

    /**
     * @When /^I run "([^"]*)" manually$/
     */
    public function iRunManually($command)
    {
        $job = $this->getJob($command);
        $this->kernel->getContainer()->get('sylius.scheduler.job.manager')->runJobAsync($job->getId());
    }

    /**
     * @Given /^There should be "([^"]*)" error logs of command "([^"]*)"$/
     */
    public function thereShouldBeErrorLogsOfCommand($numLogs, $command)
    {
        $logs = $this->logsOfCommand($command, JobStatus::FAILED);
        assertSame(count($logs), (int)$numLogs);
    }

    /**
     * @Given /^I have a job with command "([^"]*)" and schedule "([^"]*)" and active "([^"]*)"$/
     */
    public function iHaveAJobWithCommandAndScheduleAndActive($command, $schedule, $active)
    {
        $this->thereIsJob($command, $schedule);
        $job = $this->getJob($command);
        $job->setActive($active);
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * @Given /^I am not logged in as an administrator$/
     */
    public function iAmNotLoggedInAsAnAdministrator()
    {
        $this->getSecurityContext()->setToken(null);
        $this->getContainer()->get('session')->invalidate();
    }

    /**
     * @Then /^I should not be able to access the page$/
     */
    public function iShouldNotBeAbleToAccessThePage()
    {
        $this->assertSession()->addressEquals($this->generatePageUrl('sylius_backend_security_login'));
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Given /^There are no jobs$/
     */
    public function thereAreNoJobs()
    {
        $jobs = $this->em->getRepository('SyliusJobSchedulerBundle:Job')->findAll();
        foreach ($jobs as $job) {
            $this->em->remove($job);
        }
        $this->em->flush();
    }

    /**
     * @Then /^I should be able to see "([^"]*)" jobs in the list$/
     */
    public function iShouldBeAbleToSeeJobInTheList($num)
    {
        $tables = $this->getSession()->getPage()->findAll('css', $this->getJobTableSelector());
        if (!isset($tables[0])) {
            throw new \Exception(sprintf('The %d table "%s" was not found in the page'));
        }

        $rows = $tables[0]->findAll('css', 'tbody tr');
        assertEquals($num, count($rows));
    }

    /**
     * @Given /^I should see a job with "([^"]*)" value "([^"]*)" in the list$/
     */
    public function iShouldSeeAJobWithValueInTheList($columnHeader, $value)
    {
        $colIndex = $this->headerColumn($columnHeader);
        $this->theStColumnOfTheStRowInTheTableShouldContain($colIndex, 1, $this->getJobTableSelector(), $value);
    }

    /**
     * @Given /^I should see a job with "([^"]*)" in the list$/
     */
    public function iShouldSeeAJobWithInTheList($columnHeader)
    {
        $colIndex = $this->headerColumn($columnHeader);
        assertGreaterThan(0, $colIndex);
    }


    /**
     * @Given /^I should see a job with active "([^"]*)"$/
     */
    public function iShouldSeeAJobWithActive($active)
    {
        $this->theStColumnOfTheStRowInTheTableShouldContain(7, 1, 'table#job-table', $active);
    }

    /**
     * @Given /^I should see "([^"]*)" unchecked$/
     */
    public function iShouldSeeUnchecked($field)
    {
        $id = 'form_' . strtolower($field);

        assertTrue($this->getSession()->getPage()->hasUncheckedField($id));
    }

    /**
     * @Then /^I should be editing "([^"]*)" job$/
     */
    public function iShouldBeEditingJob($command)
    {
        $job = $this->getJob($command);

        $this->assertSession()->addressEquals($this->generateUrl('sylius_backend_job_update', array('id' => $job->getId())));
    }

    /**
     * @Given /^I am editing "([^"]*)" job$/
     */
    public function iAmEditingJob($command)
    {
        $job = $this->getJob($command);

        $this->getSession()->visit($this->generatePageUrl('sylius_backend_job_update', array('id' => $job->getId())));
    }

    /**
     * @Given /^I should not see "([^"]*)" job in the jobs list$/
     */
    public function iShouldNotSeeAJobInTheList($command)
    {
        $commandRowIndex = $this->commandRowIndex($command);

        assertEquals(0, $commandRowIndex);
    }

    /**
     * @Given /^I should not see job "([^"]*)" in the list$/
     */
    public function iShouldNotSeeJobInTheList($command)
    {
        $rows = $this->getSession()->getPage()->findAll('css', 'table#job-table');
        assertEquals(0, count($rows));
    }

    /**
     * @Then /^I should see something in column "([^"]*)" of "([^"]*)" job$/
     */
    public function iShouldSeeSomethingInColumnOfJob($headerColumn, $command)
    {

        $commandRowIndex = $this->commandRowIndex($command);
        $headerColumn    = $this->headerColumn($headerColumn);
        $value           = $this->tableCellValue($headerColumn, $commandRowIndex);

        assertNotEmpty($value);
    }

    /**
     * @Then /^I should see "([^"]*)" in column "([^"]*)" of "([^"]*)" job$/
     */
    public function iShouldSeeInColumnOfJob($expected, $columnHeader, $command)
    {
        $commandRowIndex = $this->commandRowIndex($command);

        $headerColumn = $this->headerColumn($columnHeader);

        $value = $this->tableCellValue($headerColumn, $commandRowIndex);
        assertEquals($expected, $value);
    }

    /**
     * @Given /^I should see the spinner next to "([^"]*)" in column "([^"]*)" of "([^"]*)" job$/
     */
    public function iShouldSeeTheSpinnerNextToInColumnOfJob($expected, $columnHeader, $command)
    {
        $commandRowIndex = $this->commandRowIndex($command);
        $headerColumn    = $this->headerColumn($columnHeader);
        $value           = $this->tableCellValue($headerColumn, $commandRowIndex);
        assertEquals($expected, $value);

        $visible = $this->isSpinnerVisible($columnHeader, $command);
        assertTrue($visible);
    }

    /**
     * @Given /^I should not see the spinner next to "([^"]*)" in column "([^"]*)" of "([^"]*)" job$/
     */
    public function iShouldNotSeeTheSpinnerNextToInColumnOfJob($expected, $columnHeader, $command)
    {
        $commandRowIndex = $this->commandRowIndex($command);
        $headerColumn    = $this->headerColumn($columnHeader);
        $value           = $this->tableCellValue($headerColumn, $commandRowIndex);
        assertEquals($expected, $value);

        $visible = $this->isSpinnerVisible($columnHeader, $command);
        assertFalse($visible);
    }

    /**
     * @When /^I click "([^"]*)" next to "([^"]*)"$/
     */
    public function iClickNextTo($button, $command)
    {
        $trIndex = $this->commandRowIndex($command);
        $trs     = $this->getSession()->getPage()->findAll('css', $this->getJobTableRowSelector());
        $tr      = $trs[$trIndex - 1];

        if (null === $tr) {
            throw new ExpectationException(sprintf('Table row with value "%s" does not exist', $command), $this->getSession());
        }

        $tr->clickLink($button);
    }

    /**
     * @Given /^I should see "([^"]*)" in the list$/
     */
    public function iShouldSeeInTheList($command)
    {
        $commandRowIndex = $this->commandRowIndex($command);
        assertGreaterThan(0, $commandRowIndex);
    }

    /**
     * @When /^I press "([^"]*)" next to "([^"]*)"$/
     */
    public function iPressNextTo($button, $command)
    {
        $trIndex = $this->commandRowIndex($command);
        $trs     = $this->getSession()->getPage()->findAll('css', $this->getJobTableRowSelector());

        $tr = $trs[$trIndex - 1];
        if (null === $tr) {
            throw new ExpectationException(sprintf('Table row with value "%s" does not exist', $command), $this->getSession());
        }

        $locator = sprintf('button:contains("%s")', $button);
        if ($tr->has('css', $locator)) {
            $tr->find('css', $locator)->press();
        }
    }

    /**
     * @Given /^I click "([^"]*)" from the job delete confirmation modal$/
     */
    public function iClickFromTheJobDeleteConfirmationModal($button)
    {
        $this->iClickOnConfirmationModal($button, 'confirmation-modal');
    }

    /**
     * @Given /^I click "([^"]*)" from the job run confirmation modal$/
     */
    public function iClickFromTheJobRunConfirmationModal($button)
    {
        $this->iClickOnConfirmationModal($button, 'run-job-confirmation-modal');
    }

    /**
     * @Then /^I should see a log for job "([^"]*)"$/
     */
    public function iShouldSeeALogForJob($command)
    {
        $this->iShouldSeeRowsInTheTable(1, 'table#log-table');
    }

    /**
     * @Then /^I should see "([^"]*)" in the log modal$/
     */
    public function iShouldSeeInTheLogModal($string)
    {
        $id = '#logOutputModal';
        $this->assertSession()->elementExists('css', $id);

        $modalContainer = $this->getSession()->getPage()->find('css', $id);
        $this->getSession()->wait(1000);

        if (!preg_match('/in/', $modalContainer->getAttribute('class'))) {
            throw new \Exception('The confirmation modal was not opened...');
        }
        $modalBody = $modalContainer->find('css', '.modal-body');
        if ($modalBody) {
            $text = $modalBody->getText();
        } else {
            $text = '-1';
        }
        assertEquals($string, $text);
    }

    /**
     * @Given /^there are following jobs:$/
     * @Given /^the following jobs exist:$/
     */
    public function thereAreJobs(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereisJob($data['command'], $data['schedule']);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created country "([^""]*)"$/
     * @Given /^there is country "([^""]*)"$/
     */
    public function thereIsJob($command, $schedule = null, $flush = true)
    {
        $job = new Job();
        $job->setCommand($command);
        $job->setSchedule($schedule);
        $this->em->persist($job);

        if ($flush) {
            $this->em->flush();
        }

        return $job;
    }

    /**
     * Checks that the specified cell (column/row) of the table's body contains the specified text
     *
     * @Then /^the (?P<colIndex>\d+)(?:st|nd|rd|th) column of the (?P<rowIndex>\d+)(?:st|nd|rd|th) row in the "(?P<table>[^"]*)" table should contain "(?P<text>[^"]*)"$/
     */
    public function theStColumnOfTheStRowInTheTableShouldContain($colIndex, $rowIndex, $table, $text)
    {
        $rowSelector = sprintf('%s tbody tr', $table);
        $rows        = $this->getSession()->getPage()->findAll('css', $rowSelector);

        if (!isset($rows[$rowIndex - 1])) {
            throw new \Exception(sprintf("The row %d was not found in the %s table", $rowIndex, $table));
        }

        $row         = $rows[$rowIndex - 1];
        $colSelector = sprintf('td', $table);
        $cols        = $row->findAll('css', $colSelector);

        if (!isset($cols[$colIndex - 1])) {
            throw new \Exception(sprintf("The column %d was not found in the row %d of the %s table", $colIndex, $rowIndex, $table));
        }

        $actual = $cols[$colIndex - 1]->getText();

        assertContains($text, $actual);
    }

    /**
     * Checks that the specified table contains the specified number of rows in its body
     *
     * @Then /^(?:|I )should see (?P<nth>\d+) rows? in the "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeRowsInTheTable($nth, $table)
    {
        $this->iShouldSeeRowsInTheNthTable($nth, 1, $table);
    }

    /**
     * @Given /^I fill "([^"]*)" with "([^"]*)"$/
     */
    public function iFillWith($field, $value)
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * @Given /^I am editing the global settings$/
     */
    public function iAmEditingTheGlobalSettings()
    {
        $this->getSession()->visit($this->generateUrl('sylius_backend_scheduler_settings_edit'));
    }

    /**
     * @Given /^I set browser window size to "([^"]*)" x "([^"]*)"$/
     */
    public function iSetBrowserWindowSizeToX($width, $height)
    {
        $this->getSession()->resizeWindow((int)$width, (int)$height);
    }

    protected function iClickOnConfirmationModal($button, $id)
    {
        $id = '#' . $id;
        $this->assertSession()->elementExists('css', $id);

        $modalContainer = $this->getSession()->getPage()->find('css', $id);
        $primaryButton  = $modalContainer->find('css', sprintf('a:contains("%s")', $button));

        $this->getSession()->wait(10000);

        if (!preg_match('/in/', $modalContainer->getAttribute('class'))) {
            throw new \Exception('The confirmation modal was not opened...');
        }

        $this->getSession()->wait(10000);

        $primaryButton->press();
    }

    /**
     * Take screenshot when step fails.
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(AfterStepScope $event)
    {
        $a = $event->getTestResult()->getResultCode();
        if (TestResult::FAILED === $event->getTestResult()->getResultCode()) {
            $driver = $this->getSession()->getDriver();
            if (!($driver instanceof Selenium2Driver)) {
                return;
            }
            $directory = 'build/behat/' . $event->getFeature()->getTitle();
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $filename = sprintf('%s_%s_%s.%s', $this->getMinkParameter('browser_name'), date('c'), uniqid('', true), 'png');
            file_put_contents($directory . '/' . $filename, $driver->getScreenshot());
        }
    }

    /**
     * Checks that the specified table contains the specified number of rows in its body
     *
     * @Then /^(?:|I )should see (?P<nth>\d+) rows in the (?P<index>\d+)(?:st|nd|rd|th) "(?P<table>[^"]*)" table$/
     */
    public function iShouldSeeRowsInTheNthTable($nth, $index, $table)
    {
        $tables = $this->getSession()->getPage()->findAll('css', $table);
        if (!isset($tables[$index - 1])) {
            throw new \Exception(sprintf('The %d table "%s" was not found in the page', $index, $table));
        }

        $rows = $tables[$index - 1]->findAll('css', 'tbody tr');
        assertEquals($nth, count($rows));
    }

    private function getOutput($command)
    {
        $job  = $this->getJob($command);
        $logs = $this->em->getRepository('SyliusJobSchedulerBundle:JobLog')->findBy(array('job' => $job->getId()));

        if (count($logs) > 0) {
            $log    = $logs[0];
            $output = $log->getOutput();
        } else {
            $output = '';
        }

        return $output;
    }

    private function getJob($command)
    {
        return $job = $this->em->getRepository('SyliusJobSchedulerBundle:Job')->findOneByCommand($command);
    }

    private function commandRowIndex($command)
    {
        $colIndex        = $this->headerColumn('Command');
        $commandRowIndex = 0;
        $rows            = $this->getSession()->getPage()->findAll('css', $this->getJobTableRowSelector());
        foreach ($rows as $key => $row) {
            $cols = $row->findAll('css', '');
            if (strtoupper($cols[$colIndex]->getText()) === strtoupper($command)) {
                $commandRowIndex = $key + 1;
                break;
            }
        }

        return $commandRowIndex;
    }

    private function headerColumn($headerColumn)
    {
        $cols   = $this->getTableHeaderRow();
        $column = '';

        foreach ($cols as $key => $col) {
            if (strtoupper($col->getText()) === strtoupper($headerColumn)) {
                $column = $key;
                break;
            }
        }

        return $column + 1;
    }

    private function tableCellValue($colIndex, $rowIndex)
    {

        $rows = $this->getSession()->getPage()->findAll('css', $this->getJobTableRowSelector());

        if (!isset($rows[$rowIndex - 1])) {
            throw new \Exception(sprintf("The row %d was not found in the %s table", $rowIndex, 'table#job-table tr'));
        }

        $row  = $rows[$rowIndex - 1];
        $cols = $row->findAll('css', 'td');

        if (!isset($cols[$colIndex - 1])) {
            throw new \Exception(sprintf("The column %d was not found in the row %d of the %s table", $colIndex, $rowIndex, 'table#job-table tr'));
        }

        $actual = $cols[$colIndex - 1]->getText();

        return $actual;
    }

    private function tableCell($colIndex, $rowIndex)
    {

        $rows = $this->getSession()->getPage()->findAll('css', $this->getJobTableRowSelector());

        if (!isset($rows[$rowIndex - 1])) {
            throw new \Exception(sprintf("The row %d was not found in the %s table", $rowIndex, 'table#job-table tr'));
        }

        $row  = $rows[$rowIndex - 1];
        $cols = $row->findAll('css', 'td');

        if (!isset($cols[$colIndex - 1])) {
            throw new \Exception(sprintf("The column %d was not found in the row %d of the %s table", $colIndex, $rowIndex, 'table#job-table tr'));
        }

        $actual = $cols[$colIndex - 1];

        return $actual;
    }

    private function isSpinnerVisible($columnHeader, $command)
    {
        $commandRowIndex = $this->commandRowIndex($command);
        $headerColumn    = $this->headerColumn($columnHeader);

        $tableCell = $this->tableCell($headerColumn, $commandRowIndex);
        $spinner   = $tableCell->find('css', 'img');

        return $spinner->isVisible();
    }

    private function logsOfCommand($command, $status)
    {
        $job = $this->getJob($command);

        return $this->em->getRepository('SyliusJobSchedulerBundle:JobLog')->findBy(array('job' => $job->getId(), 'status' => $status));
    }

    private function getJobTableSelector()
    {
        return 'table#job-table';
    }

    private function getJobTableHeaderSelector()
    {
        return 'table#job-table th';
    }

    private function getJobTableRowSelector()
    {
        return 'table#job-table tr';
    }

    private function getTableHeaderRow()
    {
        return $this->getSession()->getPage()->findAll('css', $this->getJobTableHeaderSelector());
    }
}
