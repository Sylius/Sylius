@job_scheduler
Feature: Scheduler command priority
    In order to run  commands with different priorities
    As an admin user
    I need to show simple scenario

    Background:
        Given Settings are "enabled"

    Scenario: Jobs are run in priority order
        Given I have a job with command "echo 'Hello World 4'" and schedule "* * * * * *" and priority "1"
        And I have a job with command "echo 'Hello World 1'" and schedule "* * * * * *" and priority "2"
        And I have a job with command "echo 'Hello World 3'" and schedule "* * * * * *" and priority "3"
        And I have a job with command "echo 'Hello World 2'" and schedule "* * * * * *" and priority "4"
        When I run "sylius:run_active_jobs" command
        And I wait "6" seconds
        Then "echo 'Hello World 4'" should have been executed before "echo 'Hello World 1'"
        And "echo 'Hello World 1'" should have been executed before "echo 'Hello World 3'"
        And "echo 'Hello World 3'" should have been executed before "echo 'Hello World 2'"