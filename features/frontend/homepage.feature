@homepage
Feature: Store homepage
    In order to access and browse the store
    As a visitor
    I want to be able to see the homepage

    Scenario: Viewing the homepage at website root
         When I go to the website root
         Then I should be on the homepage
          And I should see "Welcome to Sylius"
