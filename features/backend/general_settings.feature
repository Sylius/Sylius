Feature: General settings
    In order configure my store SEO fields
    As a store owner
    I want to be able to edit general configuration

    Background:
        Given I am logged in as administrator

    Scenario: Accessing the settings form
        Given I am on the dashboard page
         When I follow "General settings"
         Then I should be on the general settings page

    Scenario: Saving the configuration
        Given I am on the general settings page
         When I press "Save changes"
         Then I should still be on the general settings page
          And I should see "Settings have been successfully updated."

    Scenario: Editing the page title
        Given I am on the general settings page
         When I fill in "Page title" with "SuperStore.com - SALE!"
          And I press "Save changes"
         Then I should still be on the general settings page
          And I should see "Settings have been successfully updated."

    Scenario: Frontend store title changes after settings update
        Given I am on the general settings page
          And I fill in "Page title" with "FooShop.com - Sale!"
          And I press "Save changes"
         When I go to the website root
         Then the page title should be "FooShop.com - Sale!"

    Scenario: Editing the meta description
        Given I am on the general settings page
         When I fill in "Meta description" with "Best fashion store on the web."
          And I press "Save changes"
         Then I should still be on the general settings page
          And I should see "Settings have been successfully updated."

    Scenario: Editing the meta keywords
        Given I am on the general settings page
         When I fill in "Meta keywords" with "clothing, fashion, trends"
          And I press "Save changes"
         Then I should still be on the general settings page
          And I should see "Settings have been successfully updated."
