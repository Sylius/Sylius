@receiving_discount
Feature: Receiving discount in the order defined by promotions' priorities
    In order to pay proper amount while buying promoted goods
    As a Visitor
    I want to have promotions applied in prioritized fashion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "The Pug Mug" priced at "$100.00"

    @ui
    Scenario: Receiving fixed discount first when priority is greater
        Given there is a promotion "Cebula Deal" with priority 1
        And it gives "$10.00" discount to every order
        And there is a promotion "Santa's Gift" with priority 4
        And it gives "20%" discount to every order
        When I add product "The Pug Mug" to the cart
        Then my cart total should be "$70.00"

    @ui
    Scenario: Receiving percentage discount first when priority is greater
        Given there is a promotion "Cebula Deal" with priority 5
        And it gives "$10.00" discount to every order
        And there is a promotion "Santa's Gift" with priority 2
        And it gives "20%" discount to every order
        When I add product "The Pug Mug" to the cart
        Then my cart total should be "$72.00"

    @ui
    Scenario: Receiving discount from exclusive promotion even if their priority is lower than that of a regular one
        Given there is a promotion "Cebula Deal" with priority 5
        And it gives "$10.00" discount to every order
        And there is an exclusive promotion "Golden Pug Market" with priority 1
        And it gives "20%" discount to every order
        When I add product "The Pug Mug" to the cart
        Then my cart total should be "$80.00"

    @ui
    Scenario: Receiving discount from an exclusive promotion with higher priority
        Given there is an exclusive promotion "Golden Pug Market" with priority 2
        And it gives "20%" discount to every order
        And there is an exclusive promotion "Sloth's Agility" with priority 5
        And it gives "$10.00" discount to every order
        When I add product "The Pug Mug" to the cart
        Then my cart total should be "$90.00"
