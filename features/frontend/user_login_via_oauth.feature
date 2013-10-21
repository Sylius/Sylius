@users @oauth
Feature: Sign in to the store via OAuth
    In order to view my orders list
    As a visitor with an OAuth account
    I need to be able to log in to the store

    Background:
        Given I am not logged in
          And I am on the store homepage

    Scenario: Get to the OAuth login page
         When I follow "Login"
          And I press the login with Amazon button
         Then I should be on the amazon.com website
          And I should see the Amazon login form
