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
    Given I provide currency 'GBP'
    When I run a command "sylius:install:setup"
    Then I should see output "Adding British Pound Sterling"

  Scenario: Registering administrator account
    Given I provide full administrator data
    When I run a command "sylius:install:setup"
    Then I should see output "Administrator account successfully registered."