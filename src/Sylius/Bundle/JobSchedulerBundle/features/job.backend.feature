@job_scheduler
Feature: Scheduler command backend administration
    In order to add, edit and delete jobs
    As an admin user
    I need to do it through a GUI

    Background:
        Given I am logged in as administrator
        And there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
            | sleep 2            | * * * * * * |
            | sleep 20           | * * * * * * |
            | sleep 1            | * * * * * * |
        And Settings are "enabled"

    Scenario: Only administrate jobs if logged in as an administrator
        Given I am not logged in as an administrator
        When I go to the job index page
        Then I should not be able to access the page

    Scenario: Seeing empty index of jobs
        Given There are no jobs
        And I am on the dashboard page
        When I follow "Jobs"
        Then I should see "There are no jobs"

    Scenario: View a list of jobs
        Given I am on the dashboard page
        When I follow "Jobs"
        Then I should be able to see "4" jobs in the list

    Scenario: Fields are listed in the index
        Given I am on the dashboard page
        When I follow "Jobs"
        Then I should be on the sylius backend job index page
        And I should see a job with "LAST STATUS" value "-" in the list
        And I should see a job with "COMMAND" value "echo 'Hello World'" in the list
        And I should see a job with "ACTIVE" value "YES" in the list
        And I should see a job with "SCHEDULE" value "* * * * * *" in the list
        And I should see a job with "IS RUNNING" value "NO" in the list
        And I should see a job with "LAST RUN" in the list

    Scenario: Accessing the job creation form
        Given I am on the dashboard page
        When I follow "Jobs"
        And I follow "Create new job"
        Then I should be on the sylius backend job create page

    Scenario: Submitting an empty form
        Given I am on the sylius backend job create page
        When I press "Create"
        Then I should see "This value should not be blank."

    Scenario: Submitting a form without command filled
        Given I am on the sylius backend job create page
        When I fill "Schedule" with "* * * * * *"
        And I press "Create"
        Then I should see "This value should not be blank."

    Scenario: Submitting a form without schedule filled
        Given I am on the sylius backend job create page
        When I fill "Command" with "echo 'Hi'"
        And I press "Create"
        Then I should see "This value should not be blank."

    Scenario: Submitting a valid form
        Given There are no jobs
        And I am on the sylius backend job create page
        When I fill "Command" with "echo 'Hi'"
        And I fill "Schedule" with "1 2 3 4 5 6"
        And I fill "Description" with "My job"
        And I fill "Environment" with "PROD"
        And I fill "Server type" with "PRIMARY"
        And I fill "Priority" with "9"
        And I press "Create"
        Then I should be on the sylius backend job index page
        And I should see a job with "COMMAND" value "echo 'Hi'" in the list
        And I should see a job with "LAST STATUS" value "-" in the list
        And I should see a job with "SCHEDULE" value "1 2 3 4 5 6" in the list
        And I should see a job with "ACTIVE" value "YES" in the list

    Scenario: Accessing the editing form from the list
        Given I am on the dashboard page
        When I follow "Jobs"
        And I click "edit" next to "echo 'Hello World'"
        Then I should be editing "echo 'Hello World'" job

    Scenario: Updating the fields
        Given I am editing "echo 'Hello World'" job
        And I fill "Command" with "echo 'Hi'"
        And I fill "Schedule" with "1 2 3 4 5 6"
        And I fill "Description" with "My job"
        And I fill "Environment" with "PROD"
        And I fill "Server type" with "PRIMARY"
        And I fill "Priority" with "9"
        When I press "Save changes"
        Then I should be on the sylius backend job index page
        And I should see a job with "COMMAND" value "echo 'Hi'" in the list
        And I should see a job with "LAST STATUS" value "-" in the list
        And I should see a job with "SCHEDULE" value "1 2 3 4 5 6" in the list
        And I should see a job with "ACTIVE" value "YES" in the list
        And I should not see "echo 'Hello World'" job in the jobs list

    Scenario: Accessing the global settings
        Given I am on the sylius backend job index page
        When I follow "Edit Global Scheduler Settings"
        Then I should be on the scheduler settings edit page

    Scenario: Editing the global  settings
        Given I am editing the global settings
        When I uncheck "Enabled"
        And I press "Save changes"
        Then I should be on the scheduler settings edit page
        And I should see "Enabled" unchecked

    Scenario: Closing the global  settings
        Given I am editing the global settings
        When I follow "Cancel"
        Then I should be on the sylius backend job index page

    @javascript
    Scenario: Deleting a job via the list
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        When I press "delete" next to "echo 'Hello World'"
        And I click "delete" from the job delete confirmation modal
        Then I should see "Job has been successfully deleted."
        But I should not see "echo 'Hello World'" job in the jobs list

    @javascript
    Scenario: Running a job manually
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        When I click "Run" next to "sleep 2"
        And I click "Run" from the job run confirmation modal
        And I wait "3" seconds
        Then I should see something in column "LAST RUN" of "sleep 2" job

    @javascript
    Scenario: Deleting a job with logs via the list
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        And I click "Run" next to "echo 'Hello World'"
        And I click "Run" from the job run confirmation modal
        And I wait "2" seconds
        When I press "delete" next to "echo 'Hello World'"
        And I wait "2" seconds
        And I click "delete" from the job delete confirmation modal
        And I wait "5" seconds
        Then I should see "Job has been successfully deleted."
        But I should not see "echo 'Hello World'" job in the jobs list

    @javascript
    Scenario: Running a job manually displays new status and spinner
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        When I click "Run" next to "sleep 20"
        And I click "Run" from the job run confirmation modal
        And I wait "5" seconds
        Then I should see "YES  " in column "IS RUNNING" of "sleep 20" job
        And I should see the spinner next to "YES  " in column "IS RUNNING" of "sleep 20" job

    @javascript
    Scenario: After completion of running a job manually, it displays new status and hides spinner
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        When I click "Run" next to "sleep 1"
        And I click "Run" from the job run confirmation modal
        And I wait "7" seconds
        Then I should see "NO  " in column "IS RUNNING" of "sleep 1" job
        And I should not see the spinner next to "NO" in column "IS RUNNING" of "sleep 1" job

    @javascript
    Scenario: Running a job manually should create a log with output
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        And I click "Run" next to "echo 'Hello World'"
        And I click "Run" from the job run confirmation modal
        And I wait "4" seconds
        When I click "Show logs" next to "echo 'Hello World'"
        And I wait "3" seconds
        Then I should see a log for job "echo 'Hello World'"
        And I follow "See output"
        And I wait "3" seconds
        And I should see "Hello World" in the log modal

    @javascript
    Scenario: Running a job manually should create a error log with output
        Given I am on the sylius backend job index page
        And I set browser window size to "1024" x "768"
        And I click "Run" next to "sleep 20"
        And I click "Run" from the job run confirmation modal
        And I click "Run" next to "sleep 20"
        And I click "Run" from the job run confirmation modal
        And I wait "2" seconds
        When I click "Show logs" next to "sleep 20"
        Then I should see "failed"