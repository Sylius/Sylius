@order_history
Feature: Tracking changes on order addresses
    In order to be aware of order's addresses changes on order
    As an Administrator
    I want to be able to track changes on order's addresses
    
    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Italian suit" priced at "$4000.00"
        And there is a customer "barney@stinson.com" that placed an order "#00000001"
        And the customer bought a single "Italian suit"
        When I am logged in as an administrator

    @ui
    Scenario: Browsing order's addresses history after changing it by customer
        Given the customer "Barney Stinson" addressed it to "East 84st Street and 3rd Avenue", "10118" "New York" in the "United States" with identical billing address
        And the customer changed shipping address' street to "211 Madison Avenue"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        When I browse order's "#00000001" history
        Then there should be 2 changes in the registry

    @ui
    Scenario: Browsing order's addresses history after changing it by administrator
        Given the customer "Barney Stinson" addressed it to "East 84st Street and 3rd Avenue", "10118" "New York" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        When I want to modify a customer's shipping address of this order
        And I specify their shipping address as "New York", "150 W. 85th Street", "10028", "United States" for "Ted Mosby"
        And I save my changes
        And I browse order's "#00000001" history
        Then there should be 2 changes in the registry
