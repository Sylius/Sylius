Feature: Store dashboard
    In order to have an overview of my business
    As a store owner
    I need to be able to see sales info in backend dashboard

    Background:
        Given I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
          And there are products:
            | name          | price |
            | Mug           | 5.99  |
            | Sticker       | 10.00 |
          And the following shipping methods exist:
            | category | zone         | name        |
            |          | German lands | FedEx       |
          And the following orders were placed:
            | user              | address                                                | shippingMethod |
            | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany | FedEx          |
            | lars@example.com  | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       | FedEx          |
        And order #000001 has following items:
            | product | quantity |
            | Mug     | 2        |
        And order #000002 has following items:
            | product | quantity |
            | Mug     | 1        |
            | Sticker | 4        |

    Scenario: Viewing the dashboard at website root
        Given I am on the dashboard page
         Then I should see "Administration dashboard"

    Scenario: Viewing sales info for last week
         Given I am on the dashboard page
          Then I should see last week revenue with value "€57.97" in the list
          Then I should see last week orders with value "2" in the list

    Scenario: Viewing recent orders
         Given I am on the dashboard page
          Then I should see 2 orders in the list

    Scenario: Viewing recent users
         Given I am on the dashboard page
          Then I should see 3 users in the list
