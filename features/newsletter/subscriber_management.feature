@newsletter
Feature: Subscriber management
  In order to
  As a store owner
  I want to be able to manage subscribers

  Background:
    Given there is default currency configured
    And there are following subscription lists:
      | name          | description |
      | Subscription1 |             |
      | Subscription2 |             |
    And there are following subscribers:
      | email               |
      | john@doe.com        |
      | johndoe@example.com |
    And I am logged in as administrator

  Scenario: Browsing all subscribers
    Given I am on the subscriber index page
    Then I should see 2 subscribers in the list
    And I should see subscriber with email "john@doe.com" in the list