@accessing_cart
Feature: Allowing access only for correctly logged in users
    In order not to allow to use a cart by anybody who does not have proper access
    As a Store Owner
    I want only users with proper permissions to have access to the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark T-shirt" priced at "$12.00"
        And the store allows paying offline
        And the store has "UPS" shipping method with "$20.00" fee

    @api
    Scenario: Accessing to the cart by the visitor
        When the visitor adds "Stark T-Shirt" product to the cart
        Then the visitor can see "Stark T-Shirt" product in the cart

    @api
    Scenario: Accessing to add address to the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        When the visitor specify the email as "jon.snow@example.com"
        And the visitor specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor completes the addressing step
        Then the visitor should have checkout address step completed

    @api
    Scenario: Accessing to add shipping method to the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        And the visitor has specified the email as "jon.snow@example.com"
        And the visitor has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor has completed the addressing step
        When the visitor proceed with "UPS" shipping method
        Then the visitor should have checkout shipping method step completed

    @api
    Scenario: Accessing to add payment method to the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        And the visitor has specified the email as "jon.snow@example.com"
        And the visitor has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor has completed the addressing step
        And the visitor has proceeded "UPS" shipping method
        When the visitor proceed with "offline" payment
        Then the visitor should have checkout payment step completed

    @api
    Scenario: Accessing to complete the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        And the visitor has specified the email as "jon.snow@example.com"
        And the visitor has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor has completed the addressing step
        And the visitor has proceeded "UPS" shipping method
        And the visitor has proceeded "offline" payment
        When the visitor confirm his order
        Then the visitor should see the thank you page

    @api
    Scenario: Accessing to increase quantity of an item in the cart by the visitor
        Given the visitor has product "Stark T-Shirt" in the cart
        When the visitor change product "Stark T-Shirt" quantity to 2 in his cart
        Then the visitor should see product "Stark T-Shirt" with quantity 2 in his cart

    @api
    Scenario: Accessing to the cart by the logged in customer
        Given the customer logged in
        When the customer adds "Stark T-Shirt" product to the cart
        Then the customer can see "Stark T-Shirt" product in the cart

    @api
    Scenario: Accessing to add address to the cart by the customer
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        When the customer specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer completes the addressing step
        Then the customer should have checkout address step completed

    @api
    Scenario: Accessing to add shipping method to the cart by the customer
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer has specified the email as "jon.snow@example.com"
        And the customer has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer has completed the addressing step
        When the customer proceed with "UPS" shipping method
        Then the customer should have checkout shipping method step completed

    @api
    Scenario: Accessing to add payment method to the cart by the customer
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer has completed the addressing step
        And the customer has proceeded "UPS" shipping method
        When the customer proceed with "offline" payment
        Then the customer should have checkout payment step completed

    @api
    Scenario: Accessing to complete the cart by the customer
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer has completed the addressing step
        And the customer has proceeded "UPS" shipping method
        And the customer has proceeded "offline" payment
        When the customer confirm his order
        Then the customer should see the thank you page

    @api
    Scenario: Accessing to increase quantity of an item in the cart by the customer
        Given the customer has product "Stark T-Shirt" in the cart
        When the customer change product "Stark T-Shirt" quantity to 2 in his cart
        Then the customer should see product "Stark T-Shirt" with quantity 2 in his cart

    @api
    Scenario: Denying access to the customers cart by the visitor
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer logged out
        And there is the visitor
        When the visitor try to see the summary of customer's cart
        Then the visitor has no access to customer's cart

    @api
    Scenario: Denying access to add product to the customer cart by the visitor
        Given the customer logged in
        And the customer has created empty cart
        And the customer logged out
        When the visitor try to add product "Stark T-Shirt" in the customer cart
        Then the visitor has no access to customer's cart

    @api
    Scenario: Denying access to add address to the customer cart by the visitor
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer logged out
        When the visitor specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the visitor try to complete the addressing step in the customer cart
        Then the visitor has no access to customer's cart

    @api
    Scenario: Denying access to add shipping method to the customer cart by the visitor
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer has completed the addressing step
        And the customer logged out
        Then the visitor has no access to proceed with "UPS" shipping method in the customer cart

    @api
    Scenario: Denying access to add payment method to the customer cart by the visitor
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer has completed the addressing step
        And the customer has proceeded "UPS" shipping method
        And the customer logged out
        Then the visitor has no access to proceed with "offline" payment in the customer cart

    @api
    Scenario: Denying access to complete the customer cart by the visitor
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer has specified address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And the customer has completed the addressing step
        And the customer has proceeded "UPS" shipping method
        And the customer has proceeded "offline" payment
        And the customer logged out
        Then the visitor has no access to confirm the customer order

    @api
    Scenario: Denying to increase quantity of an item in the customer cart by the visitor
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer logged out
        Then the visitor has no access to change product "Stark T-Shirt" quantity to 2 in the customer cart

    @api
    Scenario: Accessing to the customers cart by the admin
        Given the customer logged in
        And the customer has product "Stark T-Shirt" in the cart
        And the customer logged out
        And there is logged in the administrator
        When the administrator try to see the summary of customer's cart
        Then the administrator should see "Stark T-shirt" product with quantity 1 in the customer cart

    @api
    Scenario: Accessing to the visitors cart by the admin
        Given there is the visitor
        And the visitor has product "Stark T-Shirt" in the cart
        And there is logged in the administrator
        When the administrator try to see the summary of customer's cart
        Then the administrator should see "Stark T-shirt" product with quantity 1 in the visitor cart
