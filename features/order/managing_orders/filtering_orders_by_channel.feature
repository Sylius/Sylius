@managing_orders
Feature: Filtering orders by a channel
    In order to filter orders by a specific channel
    As an Administrator
    I want to be able to filter orders on the list

    Background:
        Given the store operates on a channel named "Web-EU"
        And the store also operates on a channel named "Web-US"
        And the store has customer "Mike Ross" with email "ross@teammike.com"
        And this customer has placed an order "#00000001" on a channel "Web-EU"
        And this customer has also placed an order "#00000002" on a channel "Web-EU"
        And this customer has also placed an order "#00000003" on a channel "Web-US"
        And I am logged in as an administrator

    @ui
    Scenario: Filtering orders by a chosen channel
        When I browse orders
        And I choose "Web-EU" as a channel filter
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#00000001" number
        And I should see an order with "#00000002" number
        But I should not see an order with "#00000003" number

    @ui
    Scenario: Filtering orders by an another channel
        When I browse orders
        And I choose "Web-US" as a channel filter
        And I filter
        Then I should see a single order in the list
        And I should see an order with "#00000003" number
        But I should not see an order with "#00000001" number
        And I should not see an order with "#00000002" number
