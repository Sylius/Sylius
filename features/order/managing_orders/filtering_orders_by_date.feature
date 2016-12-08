@managing_orders
Feature: Filtering orders
    In order to filter orders in specific period of time
    As an Administrator
    I want to be able to filter orders on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has customer "Mike Ross" with email "ross@teammike.com"
        And this customer has placed an order "#00000001" at "2016-12-05 08:00:00"
        And this customer has also placed an order "#00000002" at "2016-12-05 09:00:00"
        And this customer has also placed an order "#00000003" at "2016-12-05 10:00:00"
        And I am logged in as an administrator

    @ui
    Scenario: Filtering orders by date from
        When I browse orders
        And I specify filter date from as "2016-12-05 08:30:00"
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#00000002" number
        And I should see an order with "#00000003" number
        But I should not see an order with "#00000001" number

    @ui
    Scenario: Filtering orders by date to
        When I browse orders
        And I specify filter date to as "2016-12-05 09:30:00"
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#00000001" number
        And I should see an order with "#00000002" number
        But I should not see an order with "#00000003" number

    @ui
    Scenario: Filtering orders by date from to
        When I browse orders
        And I specify filter date from as "2016-12-05 08:30:00"
        And I specify filter date to as "2016-12-05 09:30:00"
        And I filter
        Then I should see a single order in the list
        And I should see an order with "#00000002" number
        But I should not see an order with "#00000001" number
        And I should not see an order with "#00000003" number
