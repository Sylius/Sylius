@legacy @account
Feature: User account orders page
    In order to follow my orders
    As a logged user
    I want to be able to track and get an invoice of my sent orders

    Background:
        Given store has default configuration
        And I am logged in user
        And the following zones are defined:
            | name        | type    | members                 |
            | Scandinavia | country | Norway, Sweden, Finland |
            | France      | country | France                  |
        And there are following shipping categories:
            | code | name    |
            | SC1  | Regular |
            | SC2  | Heavy   |
        And the following shipping methods exist:
            | code | category | zone        | name |
            | SM1  | Regular  | Scandinavia | DHL  |
            | SM2  | Heavy    | France      | UPS  |
        And the following products exist:
            | name | price | sku |
            | Mug  | 5.99  | 456 |
            | Book | 22.50 | 948 |
        And all products are assigned to the default channel
        And the following orders exist:
            | customer                | shipment                 | address                                                           |
            | sylius@example.com      | UPS, shipped, DTBHH380HG | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
            | linustorvalds@linux.com | DHL                      | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    |
            | sylius@example.com      | UPS                      | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |
            | Book    | 1        |

    Scenario: Viewing my account orders page
        Given I am on my account homepage
        When I follow "My orders / my invoices"
        Then I should be on my account orders page

    Scenario: Viewing my orders
        When I am on my account orders page
        Then I should see "All your orders"
        And I should see 2 orders in the list
        And I should see order with number "000000001" in the list
        And I should not see order with number "000000002" in the list

    Scenario: Viewing the detail of an order
        Given I am on my account orders page
        When I click "Details" near "000000001"
        Then I should see "Details of your order"
        And I should be on the order show page for 000000001
        And I should see 2 items in the list

    Scenario: Trying to view the detail of an order which is not mine
        When I go to the order show page for 000000002
        Then the response status code should be 403

    Scenario: Tracking an order that has been sent
        When I am on my account orders page
        Then I should see the following rows:
            | Number    | State                        |
            | 000000001 | %Shipped%                    |
            | 000000001 | %Tracking number DTBHH380HG% |

    Scenario: Trying to track an order that has not been sent
        When I am on my account orders page
        Then I should see the following row:
            | Number    | State         |
            | 000000003 | %Ready since% |
        But I should not see the following row:
            | Number    | State             |
            | 000000003 | %Tracking number% |

    Scenario: Tracking an order that has been sent in its details page
        When I go to the order show page for 000000001
        Then I should see "Tracking number DTBHH380HG"
        And I should see "Shipped"

    Scenario: Trying to track an order that has not been sent in its details page
        When I go to the order show page for 000000003
        Then I should see "Ready since"
        But I should not see "Tracking number"

    Scenario: Checking that an invoice is available for an order that has been sent
        When I am on my account orders page
        Then I should not see the following row:
            | Number    | Invoice |
            | 000000001 | -       |

    Scenario: Checking that an invoice is not available for an order that has not been sent
        When I am on my account orders page
        Then I should see the following row:
            | Number    | Invoice |
            | 000000003 | -       |

    Scenario: Trying to generate an invoice for an order that has not been sent
        When I go to the order invoice page for 000000003
        Then the response status code should be 404

    Scenario: Trying to generate an invoice of an order which is not mine
        When I go to the order invoice page for 000000002
        Then the response status code should be 403
