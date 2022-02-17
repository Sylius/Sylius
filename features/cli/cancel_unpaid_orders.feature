@cli
Feature: Cancel unpaid orders
    In order to manually cancel only unpaid orders
    As a developer
    I need to use command in terminal

    Scenario: Canceling unpaid orders from console
        When I run command that cancels unpaid orders
        Then I should see output "Unpaid orders has been canceled" message in terminal
