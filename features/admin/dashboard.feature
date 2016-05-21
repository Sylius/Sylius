@admin_dashboard
Feature: Statistics dashboard
  In order to have an overview of my sales
  As an Administrator
  I want to see overall statistics on my admin dashboard

  Background:
    Given the store operates on a single channel in "France"
    And the store ships everywhere for free
    And the store allows paying offline
    And I am logged in as an administrator

  @ui
  Scenario: Seeing total of sales
    Given 3 customers have placed 4 orders for total of $8566
    And then 2 more customers have placed 2 orders for total of $459
    When I open administration dashboard
    Then I should see 6 new orders
    And I should see 5 new customers
    And there should be total sales of $9025
    And my average order value should be $1504

  @todo
  Scenario: Seeing recent orders and customers
    Given 2 customers have placed 3 orders for total of $340
    When I open administration dashboard
    Then I should see 2 customers in the list
    And I should see 3 new orders in the list
