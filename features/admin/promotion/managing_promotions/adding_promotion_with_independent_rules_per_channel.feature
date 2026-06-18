@managing_promotions
Feature: Adding a new promotion with independent rules configured per channel
    In order to have complex promotion rules that differ by channel
    As an Administrator
    I want to add promotions with independent rules per channel to the registry

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on a channel named "Web-GB" in "GBP" currency
        And I am logged in as an administrator

    @api @ui @mink:chromedriver
    Scenario: Adding a new promotion with independent item total rules per channel
        When I want to create a new promotion
        And I specify its code as "INDEPENDENT_RULES_PROMO"
        And I name it "Independent Rules Promotion"
        And I add the "Item total" rule configured with "€100.00" amount for "Web-US" channel
        And I add the "Item total" rule configured with "£50.00" amount for "Web-GB" channel
        And I add the "Order fixed discount" action configured with amount of "$10.00" for "Web-US" channel
        And it is also configured with amount of "£5.00" for "Web-GB" channel
        And I add it
        Then the "Independent Rules Promotion" promotion should be successfully created

    @api @ui @mink:chromedriver
    Scenario: Adding a new promotion with different rule types per channel
        When I want to create a new promotion
        And I specify its code as "DIFFERENT_RULE_TYPES"
        And I name it "Different Rule Types Per Channel"
        And I add the "Item total" rule configured with "€100.00" amount for "Web-US" channel
        And I add the "Cart quantity" rule with minimum 2 items for "Web-GB" channel
        And I add the "Item fixed discount" action configured with amount of "$10.00" for "Web-US" channel
        And it is also configured with amount of "£5.00" for "Web-GB" channel
        And I add it
        Then the "Different Rule Types Per Channel" promotion should be successfully created
