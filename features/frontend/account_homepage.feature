Feature: User account homepage
    In order to access and manage my personal information
    As a logged user
    I want to be able to see my account homepage

    Background:
        Given I am logged in user

    Scenario: Viewing the homepage of my account
        Given I am on the store homepage
         When I follow "My account"
         Then I should be on my account homepage
          And I should see "Welcome to your space"
