@installer @cli
Feature: Sylius Install Feature
    In order to install Sylius via CLI
    As a Developer
    I want to run an installation command

    Scenario: Registering administrator account
        Given I provide full administrator data
        When I run Sylius CLI installer
        Then I should see output "Administrator account successfully registered."

    Scenario: Trying to register administrator account without name
        Given I do not provide a name
        When I run Sylius CLI installer
        Then I should see output "Your firstname: This value should not be blank"

    Scenario: Trying to register administrator account without surname
        Given I do not provide a surname
        When I run Sylius CLI installer
        Then I should see output "Lastname: This value should not be blank"

    Scenario: Trying to register administrator account without email
        Given I do not provide an email
        When I run Sylius CLI installer
        Then I should see output "E-Mail: This value should not be blank"

    Scenario: Trying to register administrator account with an incorrect email
        Given I do not provide a correct email
        When I run Sylius CLI installer
        Then I should see output "E-Mail: This value is not a valid email address."
