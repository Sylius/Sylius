@cli
Feature: Canceling unpaid orders
    In order to have my orders list free from completed but unpaid orders
    As a Developer
    I want to have unpaid orders cancelled

    Scenario: Canceling unpaid orders
        When the unpaid orders has been cancelled
        Then I should see be informed that unpaid orders have been cancelled
