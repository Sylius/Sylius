@account
Feature: User account homepage
    In order to access and manage my personal information
    As a logged user
    I want to be able to see my account homepage

    Scenario: Displaying the my account section only to logged users
        Given I am on the store homepage
         Then I should not see "My account"

    Scenario: Viewing the homepage of my account
        Given I am on the store homepage
          And I am logged in user
         When I follow "My account"
         Then I should be on my account homepage
          And I should see "Welcome to your space"
