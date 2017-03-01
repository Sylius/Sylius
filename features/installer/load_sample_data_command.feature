@installer @cli
Feature: Load sample data feature
    In order to have sample data in Sylius
    As a Developer
    I want to run a command that loads sample data

    Scenario: Running install sample data command
        Given I run Sylius Install Load Sample Data command
        And I confirm loading sample data
        Then the command should finish successfully
