@applying_promotion_rules
Feature: Receiving discount based on items total
    In order to pay decreased amount for my order during promotion
    As a Visitor
    I want to have promotion applied when my items total is qualified

    Background:
        Given the store operates on a channel named "Web Channel"
        And the store operates on another channel named "Mobile Channel"
        And the store has a product "PHP T-Shirt" priced at "$80.00" available in channel "Web Channel" and channel "Mobile Channel"
        And there is a promotion "Holiday promotion"

    @todo
    Scenario: Receiving discount when buying items for the required total value and in proper channel
        Given the promotion gives "$10.00" discount to every order with items total at least "$80.00" in "Mobile Channel"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$70.00"
        And my discount should be "-$10.00"

    @todo
    Scenario: Not receiving discount when buying items for the required total value but in wrong channel
        Given the promotion gives "$10.00" discount to every order with items total at least "$80.00" in "Mobile Channel"
        And the current channel is "Web Channel"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And there should be no discount

    @todo
    Scenario: Receiving discount when buying items for more than required total value
        Given the promotion gives "$10.00" discount to every order with items total at least "$90.00" in "Mobile Channel"
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$150.00"
        And my discount should be "-$10.00"

    @todo
    Scenario: Not receiving discount when buying items for more than required total value but in wrong channel
        Given the promotion gives "$10.00" discount to every order with items total at least "$90.00" in "Mobile Channel"
        And the current channel is "Web Channel"
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$160.00"
        And there should be no discount

    @todo
    Scenario: Not receiving discount when buying items for less than required total value
        Given the promotion gives "$10.00" discount to every order with items total at least "$100.00" in "Mobile Channel"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And there should be no discount

    @todo
    Scenario: Receiving discount when buying different products for more than the required total value
        Given the store has a product "Symfony T-Shirt" priced at "$60.00"
        And the promotion gives "$10.00" discount to every order with items total at least "$200.00" in "Mobile Channel"
        When I add 2 products "PHP T-Shirt" to the cart
        And I add product "Symfony T-Shirt" to the cart
        Then my cart total should be "$210.00"
        And my discount should be "-$10.00"

    @todo
    Scenario: Not receiving discount when buying different products for more than the required total value but in wrong channel
        Given the store has a product "Symfony T-Shirt" priced at "$60.00"
        And the promotion gives "$10.00" discount to every order with items total at least "$200.00" in "Mobile Channel"
        And the current channel is "Web Channel"
        When I add 2 products "PHP T-Shirt" to the cart
        And I add product "Symfony T-Shirt" to the cart
        Then my cart total should be "$220.00"
        And there should be no discount
