@legacy @dashboard
Feature: Store dashboard
    In order to have an overview of my business
    As a store owner
    I need to be able to see sales info in backend dashboard

    Background:
        Given store has default configuration
        And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
        And there are products:
            | name    | price |
            | Mug     | 5.99  |
            | Sticker | 10.00 |
        And the following orders were placed:
            | customer          | address                                                |
            | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
            | lars@example.com  | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |
        And order #000000002 has following items:
            | product | quantity |
            | Mug     | 1        |
            | Sticker | 4        |
        And I am logged in as administrator

    Scenario: Viewing the dashboard at website root
        Given I am on the dashboard page
        Then I should see "Administration dashboard"

    Scenario: Viewing recent orders
        Given I am on the dashboard page
        Then I should see 2 orders in the list

    Scenario: Viewing recent customers
        Given I am on the dashboard page
        Then I should see 3 customers in the list
