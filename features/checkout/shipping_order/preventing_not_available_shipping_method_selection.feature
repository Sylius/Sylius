@checkout
Feature: Preventing not available shipping method selection
    In order to ship my order properly
    As a Customer
    I want to not be able to choose not available shipping methods

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Targaryen T-Shirt" priced at "$19.99"
        And I am a logged in customer

    @ui @api
    Scenario: Not being able to select disabled shipping method
        Given the store has "Raven Post" shipping method with "$10.00" fee
        And the store has disabled "Dragon Post" shipping method with "$30.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Dragon Post" shipping method

    @ui @api
    Scenario: Not being able to select shipping method not available for my shipping address
        Given there is a zone "The Rest of the World" containing all other countries
        And the store has "Dragon Post" shipping method with "$30.00" fee for the rest of the world
        And the store has "Raven Post" shipping method with "$10.00" fee within the "US" zone
        And I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Dragon Post" shipping method

    @ui @api
    Scenario: Not being able to select shipping method not available for order channel
        Given the store has "Raven Post" shipping method with "$10.00" fee not assigned to any channel
        And the store has "Dragon Post" shipping method with "$30.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Raven Post" shipping method

    @ui @api
    Scenario: Being alerted about no shipping method available
        Given there is a zone "The Rest of the World" containing all other countries
        And the store has "Dragon Post" shipping method with "$30.00" fee for the rest of the world
        And the store has disabled "Raven Post" shipping method with "$10.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Raven Post" shipping method
        And I should not be able to select "Dragon Post" shipping method
        And I should be informed that my order cannot be shipped to this address

    @ui @api
    Scenario: Not being able to select an archival shipping method
        Given the store has "Raven Post" shipping method with "$10.00" fee
        And the store has an archival "Dragon Post" shipping method with "$30.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Dragon Post" shipping method

    @ui @api
    Scenario: Not being able to select shipping method not available for shipping category of products in cart
        Given the store has "Over-sized" shipping category
        And product "Targaryen T-Shirt" belongs to "Over-sized" shipping category
        And the store has "Raven Post" shipping method with "$10.00" fee
        And the store has "Dragon Post" shipping method with "$30.00" fee
        And this shipping method requires that no units match to "Over-sized" shipping category
        And I have product "Targaryen T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Dragon Post" shipping method

    @ui @api
    Scenario: Not being able to select shipping method not available due to shipping rules
        Given the store has "Raven Post" shipping method with "$10.00" fee
        And the store has "Dragon Post" shipping method with "$30.00" fee
        And this shipping method is only available for orders over or equal to "$100.00"
        And I have product "Targaryen T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should not be able to select "Dragon Post" shipping method

    @api
    Scenario: Not being able to select inexistent shipping method
        Given the store has "Raven Post" shipping method with "$10.00" fee
        And I have product "Targaryen T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I try to select "Inexistent" shipping method
        Then I should be informed that shipping method with code "Inexistent" does not exist
