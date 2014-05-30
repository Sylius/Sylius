@job_scheduler
Feature: Scheduler command
    In order to disable tasks
    As an admin user
    I need to be sure that jobs don't run when settings are disabled

    Background:
        Given Settings are "disabled"

    Scenario: Run sylius:run_active_jobs command with global settings disabled
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
        When I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        Then There should be "0" logs of command "echo 'Hello World'"

    Scenario: Run sylius:run_active_jobs command with global settings disabled
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
            | sleep 10           | * * * * * * |
        When I run "sylius:run_active_jobs" command
        And I wait "5" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        Then There should be "0" logs of command "echo 'Hello World'"
        And There should be "0" logs of command "sleep 10"

    Scenario: Run sylius:run_active_jobs command with global settings disabled
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
            | sleep 10           | * * * * * * |
        When I run "sylius:run_active_jobs" command
        And I wait "5" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "6" seconds
        And I run "sylius:run_active_jobs" command
        And I wait "2" seconds
        Then There should be "0" logs of command "echo 'Hello World'"
        And There should be "0" logs of command "sleep 10"

    Scenario: Run one command manually when settings are disabled
        Given there are following jobs:
            | command            | schedule    |
            | echo 'Hello World' | * * * * * * |
        When I run "echo 'Hello World'" manually
        And I wait "2" seconds
        Then There should be "1" logs of command "echo 'Hello World'"
        And The last log of "echo 'Hello World'" command should contain
        """
  Hello World

  """

    Scenario: Run one command manually when settings are disabled
        Given there are following jobs:
            | command            | schedule  |
            | echo 'Hello World' | 0 4 8 1 0 |
        When I run "echo 'Hello World'" manually
        And I wait "2" seconds
        Then There should be "1" logs of command "echo 'Hello World'"
        And The last log of "echo 'Hello World'" command should contain
        """
  Hello World

  """