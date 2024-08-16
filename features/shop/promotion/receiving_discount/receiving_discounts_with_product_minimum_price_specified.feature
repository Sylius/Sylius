@receiving_discount
Feature: Receiving discounts with product minimum price specified
    In order to avoid paying less than product minimum price
    As a Visitor
    I want to receive discount for my purchase up to product minimum price

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store classifies its products as "T-Shirts"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$50.00"
        And the "PHP T-Shirt" variant has minimum price of "$45.00" in the "United States" channel
        And the store has a product "Symfony Mug" priced at "$40.00"
        And the store has a product "PHP Mug" priced at "$20.00"
        And there is a promotion "Christmas sale"

    @ui @api
    Scenario: Receiving percentage discount on a single item fulfilling minimum price criteria
        Given this promotion gives "50%" off on every product with minimum price at "$50.00"
        When I add product "T-Shirt" to the cart
        Then its price should be decreased by "$5.00"
        And my cart total should be "$45.00"

    @api
    Scenario: Receiving fixed discount for my cart
        Given there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order
        When I add product "T-Shirt" to the cart
        Then its price should be decreased by "$5.00"
        And my cart total should be "$45.00"
        And my discount should be "-$5.00"

    @ui @api
    Scenario: Receiving percentage discount on a single item fulfilling range price criteria
        Given this promotion gives "50%" off on every product priced between "$15.00" and "$50.00"
        When I add product "T-Shirt" to the cart
        Then its price should be decreased by "$5.00"
        And my cart total should be "$45.00"

    @ui @api
    Scenario: Distributing fixed discount promotion
        And this promotion gives "$10.00" off on every product with minimum price at "$10.00"
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$10.00"
        And my cart total should be "$55.00"

    @ui @api
    Scenario: Receiving fixed discount for my cart
        And the promotion gives "$10.00" discount to every order with items total at least "$20.00"
        When I add product "T-Shirt" to the cart
        Then my cart total should be "$45.00"
        And my discount should be "-$5.00"

    @ui @api
    Scenario: Receiving discount when buying more than required quantity
        Given there is a promotion "T-Shirts promotion"
        And the promotion gives "$50.00" discount to every order with quantity at least 2
        When I add 2 products "T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api
    Scenario: Distributing fixed order discount promotion
        Given the promotion gives "$20.00" discount to every order with quantity at least 2
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$15.00"
        And my cart total should be "$50.00"

    @api
    Scenario: Distributing percentage order discount promotion
        Given it gives "20%" discount to every order
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$9.00"
        And my cart total should be "$56.00"

    @api
    Scenario: Distributing discount evenly between different products when one has minimum price specified
        Given it gives "$25.00" discount to every order
        And I add product "T-Shirt" to the cart
        And I add 3 products "PHP Mug" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "T-Shirt" product should have unit price discounted by "$5.00"
        And the "PHP Mug" product should have unit prices discounted by "$6.67", "$6.67" and "$6.66"

    @api
    Scenario: Distributing discount evenly between different products when one has minimum price specified
        Given it gives "$20.00" discount to every order
        And I add 2 products "T-Shirt" to the cart
        And I add 3 products "PHP Mug" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "T-Shirt" product should have unit prices discounted by "$5.00" and "$5.00"
        And the "PHP Mug" product should have unit prices discounted by "$3.34", "$3.33" and "$3.33"

    @api
    Scenario: Distributing discount proportionally between different products when one has minimum price specified
        Given it gives "$27.00" discount to every order
        And I add 2 products "T-Shirt" to the cart
        And I add 2 products "PHP Mug" to the cart
        And I add product "Symfony Mug" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "T-Shirt" product should have unit prices discounted by "$5.00" and "$5.00"
        And the "PHP Mug" product should have unit prices discounted by "$4.25" and "$4.25"
        And the "Symfony Mug" product should have unit price discounted by "$8.50"

    @api
    Scenario: Distributing discount proportionally between different products when one has minimum price specified
        Given it gives "$36.00" discount to every order
        And the store has a "Keyboard" configurable product
        And this product has "RGB Keyboard" variant priced at "$20.00"
        And the "RGB Keyboard" variant has minimum price of "$15.00" in the "United States" channel
        And the store has a product "Mouse" priced at "$30.00"
        And the store has a product "Cup" priced at "$10.00"
        When I add 2 products "T-Shirt" to the cart
        And I add 2 products "Keyboard" to the cart
        And I add product "Mouse" to the cart
        And I add product "Cup" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "T-Shirt" product should have unit prices discounted by "$5.00" and "$5.00"
        And the "Keyboard" product should have unit prices discounted by "$5.00" and "$5.00"
        And the "Mouse" product should have unit price discounted by "$12.00"
        And the "Cup" product should have unit price discounted by "$4.00"

    @api
    Scenario: Distributing discount proportionally between different products when one has minimum price specified
        Given it gives "$12.00" discount to every order
        And the store has a product "Cup" priced at "$10.00"
        And the store has a "Keyboard" configurable product
        And this product has "RGB Keyboard" variant priced at "$20.00"
        And the "RGB Keyboard" variant has minimum price of "$19.00" in the "United States" channel
        And the store has a "Mouse" configurable product
        And this product has "RGB Mouse" variant priced at "$50.00"
        And the "RGB Mouse" variant has minimum price of "$50.00" in the "United States" channel
        And the store has a "Headphones" configurable product
        And this product has "RGB Headphones" variant priced at "$30.00"
        And the "RGB Headphones" variant has minimum price of "$29.00" in the "United States" channel
        When I add product "Cup" to the cart
        And I add product "Keyboard" to the cart
        And I add product "Mouse" to the cart
        And I add product "Headphones" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "Cup" product should have unit price discounted by "$10.00"
        And the "Keyboard" product should have unit price discounted by "$1.00"
        And the "Mouse" product should have unit price discounted by "$0.00"
        And the "Headphones" product should have unit price discounted by "$1.00"

    @api
    Scenario: Distributing discount proportionally between different products when one has minimum price specified
        Given it gives "$12.00" discount to every order
        And the store has a product "Cup" priced at "$10.00"
        And the store has a "Keyboard" configurable product
        And this product has "RGB Keyboard" variant priced at "$20.00"
        And the "RGB Keyboard" variant has minimum price of "$19.00" in the "United States" channel
        And the store has a "Mouse" configurable product
        And this product has "RGB Mouse" variant priced at "$50.00"
        And the "RGB Mouse" variant has minimum price of "$50.00" in the "United States" channel
        And the store has a "Headphones" configurable product
        And this product has "RGB Headphones" variant priced at "$30.00"
        And the "RGB Headphones" variant has minimum price of "$26.00" in the "United States" channel
        When I add product "Cup" to the cart
        And I add product "Keyboard" to the cart
        And I add product "Mouse" to the cart
        And I add product "Headphones" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "Cup" product should have unit price discounted by "$7.00"
        And the "Keyboard" product should have unit price discounted by "$1.00"
        And the "Mouse" product should have unit price discounted by "$0.00"
        And the "Headphones" product should have unit price discounted by "$4.00"

    @api
    Scenario: Distributing more than allowed discount proportionally between different products when one has minimum price specified
        Given it gives "$20.00" discount to every order
        And the store has a product "Cup" priced at "$10.00"
        And the store has a "Keyboard" configurable product
        And this product has "RGB Keyboard" variant priced at "$20.00"
        And the "RGB Keyboard" variant has minimum price of "$19.00" in the "United States" channel
        And the store has a "Mouse" configurable product
        And this product has "RGB Mouse" variant priced at "$50.00"
        And the "RGB Mouse" variant has minimum price of "$50.00" in the "United States" channel
        And the store has a "Headphones" configurable product
        And this product has "RGB Headphones" variant priced at "$30.00"
        And the "RGB Headphones" variant has minimum price of "$26.00" in the "United States" channel
        When I add product "Cup" to the cart
        And I add product "Keyboard" to the cart
        And I add product "Mouse" to the cart
        And I add product "Headphones" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "Cup" product should have unit price discounted by "$10.00"
        And the "Keyboard" product should have unit price discounted by "$1.00"
        And the "Mouse" product should have unit price discounted by "$0.00"
        And the "Headphones" product should have unit price discounted by "$4.00"

    @api
    Scenario: Distributing discount proportionally between different products when one has minimum price specified and promotion does not apply on discounted products
        Given this promotion does not apply on discounted products
        And it gives "$27.00" discount to every order
        And there is a catalog promotion "Fixed T-Shirt sale" that reduces price by fixed "$2.50" in the "United States" channel and applies on "PHP Mug" product
        And I add 2 products "T-Shirt" to the cart
        And I add 2 products "PHP Mug" to the cart
        And I add product "Symfony Mug" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "T-Shirt" product should have unit prices discounted by "$5.00" and "$5.00"
        And the "PHP Mug" product should have unit prices discounted by "$2.50" and "$2.50"
        And the "Symfony Mug" product should have unit price discounted by "$17.00"
