Feature: Store homepage
    As a visitor
    I want to be able to see homepage
    In order to access and browse the store

    Scenario: Viewing the homepage at website root
        Given I go to the website root
         Then I should be on the homepage
          And I should see "Welcome to Sylius"
