@managing_orders
Feature: Filtering orders by variants
    In order to quickly find orders containing specific variants
    As an Administrator
    I want to be able to filter orders on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store has a product "Galaxy Shirt" with code "cosmic-tee"
        And this product has "Nebula Top" variant priced at "$25.00"
        And this product also has "Neutron Sleeveless" variant priced at "$20.00"
        And the store has a product "Space Dress" with code "cosmic-dress"
        And this product has "Sundress" variant priced at "$40.00"
        And there is a customer "tanith@low.com" that placed an order "#0000001"
        And the customer bought a single "Nebula Top" variant of product "Galaxy Shirt"
        And the customer also bought a "Neutron Sleeveless" variant of product "Galaxy Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is a customer "jack@zweiblumen.com" that placed an order "#0000002"
        And the customer bought a single "Nebula Top" variant of product "Galaxy Shirt"
        And the customer also bought a "Sundress" variant of product "Space Dress"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is a customer "lirael@abhorsen.com" that placed an order "#0000003"
        And the customer bought a single "Sundress" variant of product "Space Dress"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @ui @api @javascript
    Scenario: Filtering orders by variant
        When I browse orders
        And I filter by variant "Sundress"
        Then I should see 2 orders in the list
        And I should see an order with "#0000002" number
        And I should see an order with "#0000003" number

    @ui @api @mink:chromedriver
    Scenario: Filtering orders by multiple variants of the same product
        When I browse orders
        And I filter by variants "Nebula Top" and "Neutron Sleeveless"
        Then I should see 2 orders in the list
        And I should see an order with "#0000001" number
        And I should see an order with "#0000002" number

    @ui @api @javascript
    Scenario: Filtering orders by multiple variants of different products
        When I browse orders
        And I filter by variants "Neutron Sleeveless" and "Sundress"
        Then I should see 3 orders in the list
        And I should see an order with "#0000001" number
        And I should see an order with "#0000002" number
        And I should see an order with "#0000003" number
