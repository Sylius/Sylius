@managing_promotions
Feature: Restoring removed promotion rules and actions
    In order to not lose an already configured rule or action after removing it by mistake
    As an Administrator
    I want the previously entered configuration to be restored when I add back a rule or action of the same type

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @no-api @ui @mink:chromedriver
    Scenario: Restoring the configuration of a removed rule and action while creating a new promotion
        When I want to create a new promotion
        And I specify its code as "HOLIDAY_SALE"
        And I name it "Holiday sale"
        And I add a new rule with quantity 3
        And I add the "Order fixed discount" action configured with amount of "$10.00" for "United States" channel
        And I remove its last rule
        And I remove its last action
        And I add a new rule
        And I add a new action
        And I add it
        Then the "Holiday sale" promotion should be successfully created
        And the rule quantity should be 3
        And the action amount should be "10.00"

    @no-api @ui @mink:chromedriver
    Scenario: Restoring the configuration of a removed rule and action while editing an existing promotion
        Given there is a promotion "Holiday sale" with priority 1
        And the promotion gives "$10.00" discount to every order with quantity at least 5
        When I want to modify a "Holiday sale" promotion
        Then the rule quantity should be 5
        And the action amount should be "10.00"
        When I remove its last rule
        And I remove its last action
        And I add a new rule
        And I add a new action
        And I save my changes
        Then I should be notified that it has been successfully edited
        And I want to modify a "Holiday sale" promotion
        And the rule quantity should be 5
        And the action amount should be "10.00"

    @no-api @ui @mink:chromedriver
    Scenario: Restoring rules and actions of the same type in the last-in-first-out order
        When I want to create a new promotion
        And I specify its code as "LIFO_PROMOTION"
        And I name it "LIFO promotion"
        And I add a new rule with quantity 3
        And I add a new rule with quantity 7
        And I add the "Order fixed discount" action configured with amount of "$10.00" for "United States" channel
        And I add the "Order fixed discount" action configured with amount of "$20.00" for "United States" channel
        And I remove its last rule
        And I remove its last rule
        And I remove its last action
        And I remove its last action
        And I add a new rule
        And I add a new action
        And I add it
        Then the "LIFO promotion" promotion should be successfully created
        And the rule quantity should be 3
        And the action amount should be "10.00"
