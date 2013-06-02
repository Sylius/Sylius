Feature: User account profile edition
    In order to manage my personal information
    As a logged user
    I want to be able to edit my name and my email

    Background:
        Given I am logged in user
          And I am on my account homepage

    Scenario: Viewing my personal information page
        Given I follow "My personal information"
         Then I should be on the account profile page

    Scenario: Editing my information with a blank email
        Given I am on the account profile page
         When I leave "Email" field blank
          And I fill in "Firstname" with "John"
          And I fill in "Lastname" with "Doe"
         Then I should still be on the account profile page
          And I should see "Please, enter your email"

    Scenario: Editing my information with a blank firstname
        Given I am on the account profile page
         When I fill in "Email" with "username@example.com"
          And I leave "Firstname" field blank
          And I fill in "Lastname" with "Doe"
         Then I should still be on the account profile page
          And I should see "Please, enter your firstname"

    Scenario: Editing my information with a blank lasttname
        Given I am on the account profile page
         When I fill in "Email" with "username@example.com"
          And I fill in "Firstname" with "John"
          And I leave "Lastname" field blank
         Then I should still be on the account profile page
          And I should see "Please, enter your lastname"

    Scenario: Editing my information with an invalid email
        Given I am on the account profile page
         When I fill in "Email" with "wrongemail"
          And I fill in "Firstname" with "John"
          And I fill in "Lastname" with "Doe"
         Then I should still be on the account profile page
          And I should see "This value is not a valid email address"


- 
