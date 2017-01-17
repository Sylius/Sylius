@applying_promotion_rules
Feature: Receiving a discount based on a configured promotion
    In order to pay less for my order during a promotion period
    As a Customer
    I want to receive a discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Jackets", "Trousers", "Formal attire" and "Dresses"
        And the store has a product "Black Sabbath jacket" priced at "$100.00"
        And this product belongs to "Jackets"
        And the store has a product "Iron Maiden trousers" priced at "$80.00"
        And this product belongs to "Trousers"
        And the store has a product "Metallica dress" priced at "$50.00"
        And this product belongs to "Dresses"
        And the store has a product "Rammstein bow tie" priced at "$10.00"
        And this product belongs to "Formal attire"

    @ui
    Scenario: Receiving a discount on the first order
        Given there is a promotion "First order promotion"
        And it gives "20%" off on the customer's 1st order
        And I am a logged in customer
        When I add product "Metallica Dress" to the cart
        Then my cart total should be "$40.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving a discount on items and shipping from one promotion based on items total
        Given the store has "DHL" shipping method with "$10.00" fee
        And there is a promotion "Jackets and shipping discount"
        And it gives "$10.00" off on every product classified as "Jackets" and a free shipping to every order with items total equal at least "$500.00"
        And I am a logged in customer
        When I add 7 products "Black Sabbath jacket" to the cart
        And I proceed selecting "DHL" shipping method
        Then theirs price should be decreased by "$70.00"
        And my cart total should be "$630.00"
        And my cart shipping total should be "$0.00"

    @ui
    Scenario: Receiving a discount on products from a specific taxon if an order contains products from an another taxon
        Given there is a promotion "Jacket-trousers pack"
        And it gives "10%" off on every product classified as "Jackets" if order contains any product classified as "Trousers"
        When I add product "Iron Maiden trousers" to the cart
        And I add product "Black Sabbath jacket" to the cart
        Then product "Black Sabbath jacket" price should be decreased by "$10.00"
        And my cart total should be "$170.00"

    @ui
    Scenario: Receiving a discount on items and the whole order from one promotion based on items total
        Given there is a promotion "Greatest promotion"
        And it gives "20%" off on every product classified as "Jackets" and a "$50.00" discount to every order with items total equal at least "$500.00"
        When I add 7 products "Black Sabbath jacket" to the cart
        Then theirs price should be decreased by "$140.00"
        And my cart total should be "$510.00"
        And my discount should be "-$50.00"

    @ui
    Scenario: Receiving a discount on products from multiple taxons based on products from different taxons
        Given there is a promotion "Formal attire pack"
        And it gives "10%" off on every product classified as "Formal attire" or "Dresses" if order contains any product classified as "Trousers" or "Jackets"
        When I add products "Rammstein bow tie", "Metallica dress" and "Iron Maiden trousers" to the cart
        Then product "Metallica dress" price should be decreased by "$5.00"
        And product "Rammstein bow tie" price should be decreased by "$1.00"
        And my cart total should be "$134.00"

    @ui
    Scenario: Receiving a discount on products from a specific taxon together with fixed discount on order
        Given there is a promotion "Jacket-trousers pack"
        And it gives "10%" off on every product classified as "Jackets" and "$20.00" discount on every order
        When I add product "Iron Maiden trousers" to the cart
        And I add product "Black Sabbath jacket" to the cart
        Then product "Black Sabbath jacket" price should be decreased by "$10.00"
        And my discount should be "-$20.00"
        And my cart total should be "$150.00"
