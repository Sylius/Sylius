@cli
Feature: Sylius Install Feature
  In order to install Sylius via CLI
  As an Administrator
  I want to run an installation command

  Scenario: Running install setup command
    When I run a command "sylius:install:setup"
    Then I should see output "Please enter a currency code (For example "GBP") or press ENTER to use "USD"."
    And I should see output "In which currency can your customers buy goods?"
    
  Scenario: Choosing default currency
    Given I do not provide a currency
    When I run a command "sylius:install:setup"
    Then I should see output "Adding US Dollar"

  Scenario: Choosing non-default currency
    Given I provide currency "GBP"
    When I run a command "sylius:install:setup"
    Then I should see output "Adding British Pound Sterling"

  Scenario: Registering administrator account
    Given I provide full administrator data
    When I run a command "sylius:install:setup"
    Then I should see output "Administrator account successfully registered."

  Scenario: Trying to register administrator account without name
    Given I do not provide a name
    When I run a command "sylius:install:setup"
    Then I should see output "Your firstname: This value should not be blank"

  Scenario: Trying to register administrator account without surname
    Given I do not provide a surname
    When I run a command "sylius:install:setup"
    Then I should see output "Lastname: This value should not be blank"

  Scenario: Trying to register administrator account without email
    Given I do not provide an email
    When I run a command "sylius:install:setup"
    Then I should see output "E-Mail: This value should not be blank"

  Scenario: Trying to register administrator account with an incorrect email
    Given I do not provide a correct email
    When I run a command "sylius:install:setup"
    Then I should see output "E-Mail: This value is not a valid email address."
