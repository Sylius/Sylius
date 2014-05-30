@job_scheduler
Feature: Scheduler command
    In order to run tasks
    As an admin user
    I need to be sure that jobs run when settings are enabled

    Background:
        Given Settings are "enabled"

    Scenario: Run sylius:run_active_jobs command
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
        When I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should contain
        """
  Hello World

  """

    Scenario: run_active_jobs command on a valid server type
        Given I have a job with command "echo 'Hello World'" and schedule "* * * * * *" that should run on "BIG" server type
        When I run "sylius:run_active_jobs" command on a "BIG" server type
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should contain
        """
  Hello World

  """

    Scenario: run_active_jobs command on an invalid server type
        Given I have a job with command "echo 'Hello World'" and schedule "* * * * * *" that should run on "BIG" server type
        When I run "sylius:run_active_jobs" command on a "SMALL" server type
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should not contain
        """
  Hello World

  """

    Scenario: run jobs on any server type
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
        When I run "sylius:run_active_jobs" command on a "SMALL" server type
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should contain
        """
Hello World

"""

    Scenario: run_active_jobs command on a valid environment
        Given I have a job with command "echo 'Hello World'" and schedule "* * * * * *" that should run on "PROD" environment
        When I run "sylius:run_active_jobs" command on a "PROD" environment
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should contain
        """
Hello World

"""

    Scenario: run_active_jobs command on an invalid environment
        Given I have a job with command "echo 'Hello World'" and schedule "* * * * * *" that should run on "PROD" environment
        When I run "sylius:run_active_jobs" command on a "DEV" environment
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should not contain
        """
Hello World

"""

    Scenario: run_active_jobs command on a any environment
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
        When I run "sylius:run_active_jobs" command on a "DEV" environment
        And I wait "2" seconds
        Then The last log of "echo 'Hello World'" command should contain
        """
Hello World

"""

    Scenario: Sleep command is blocked two times and error log is created
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
            | sleep 10           | * * * * * * |
        When I run "sylius:run_active_jobs" command
        And I wait "5" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "11" seconds
        Then There should be "3" logs of command "echo 'Hello World'"
        And There should be "1" logs of command "sleep 10"
        And There should be "2" error logs of command "sleep 10"

    Scenario: Sleep command is blocked one time and error log is created
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
            | sleep 10           | * * * * * * |
        When I run "sylius:run_active_jobs" command
        And I wait "5" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "6" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "11" seconds
        Then There should be "3" logs of command "echo 'Hello World'"
        And There should be "2" logs of command "sleep 10"
        And There should be "1" error logs of command "sleep 10"

    Scenario: An inactive job should not be executed when running active jobs
        Given I have a job with command "echo 'Hello World'" and schedule "* * * * * *" and active "0"
        When I run "sylius:run_active_jobs" command
        And I wait "1" seconds
        Then There should be "0" logs of command "echo 'Hello World'"

    Scenario: Run command with schedule not valid now
        Given there are following jobs:
            | command            | schedule  |
            | echo 'Hello World' | 0 4 8 1 0 |
        When I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        Then There should be "0" logs of command "echo 'Hello World'"
