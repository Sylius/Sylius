@newsletter
Feature: Subscribing
  In order to receive emails from the website owner
  As a customer
  I want to be able to subscribe to the subscription list

  Background:
    Given there are following subscription lists:
      | name          | description |
      | Subscription1 |             |
      | Subscription2 |             |
    And there are following subscribers:
      | email               |
      | john@doe.com        |
      | johndoe@example.com |
    And there is default currency configured

  Scenario: Subscribing to the subscription list
    Given I am on the store homepage
    And I fill in "Email" with "michal@lakion.com"
    And I select "Subscription1" from "Subscription Lists"
    When I press "Subscribe"
    Then I should be on the store homepage
    And I should see "Subscriber has been successfully created."

  Scenario: Subscriber's email must be unique
    Given I am on the store homepage
    And I fill in "Email" with "john@doe.com"
    When I press "Subscribe"
    Then I should be on the subscribe page
    And I should see "Email must be unique."

  Scenario: Subscriber's email can't be blank
    Given I am on the store homepage
    When I press "Subscribe"
    Then I should be on the subscribe page
    And I should see "Please enter email"

  Scenario: Subscriber's email must be valid
    Given I am on the store homepage
    And I fill in "Email" with "notemailform"
    When I press "Subscribe"
    Then I should be on the subscribe page
    And I should see "Email is invalid"
