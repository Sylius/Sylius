@account
Feature: User account orders page
  In order to follow my orders
  As a logged user
  I want to be able to track and get an invoice of my sent orders

  Background:
    Given I am logged in user
    And I am on my account homepage
    And the following zones are defined:
      | name         | type    | members                 |
      | Scandinavia  | country | Norway, Sweden, Finland |
      | France       | country | France                  |
      | USA          | country | USA                     |
    And there are following shipping categories:
      | name    |
      | Regular |
      | Heavy   |
    And the following shipping methods exist:
      | category | zone          | name  |
      | Regular  | Scandinavia   | DHL   |
      | Heavy    | USA           | FedEx |
      |          | France        | UPS   |
    And the following products exist:
      | name          | price | sku |
      | Mug           | 5.99  | 456 |
      | Sticker       | 10.00 | 213 |
      | Book          | 22.50 | 948 |
    And the following orders exist:
      | user                    | shipment                 | address                                                           |
      | sylius@example.com       | UPS, shipped, DTBHH380HG | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
      | ianmurdock@debian.org   | FedEx                    | Ian Murdock, 3569 New York Avenue, CA 92801, San Francisco, USA   |
      | ianmurdock@debian.org   | FedEx                    | Ian Murdock, 3569 New York Avenue, CA 92801, San Francisco, USA   |
      | linustorvalds@linux.com | DHL                      | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    |
      | linustorvalds@linux.com | DHL                      | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    |
      | sylius@example.com       | UPS                      | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
      | sylius@example.com       | UPS                      | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
      | linustorvalds@linux.com | DHL                      | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    |
      | sylius@example.com       | UPS                      | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
      | sylius@example.com       | UPS                      | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
      | ianmurdock@debian.org   | FedEx                    | Ian Murdock, 3569 New York Avenue, CA 92801, San Francisco, USA   |
    # order that has been sent
    And order #000000001 has following items:
      | product  | quantity |
      | Mug      | 2        |
      | Sticker  | 4        |
      | Book     | 1        |
    # order that has not been sent yet
    And order #000000007 has following items:
      | product  | quantity |
      | Mug      | 5        |
      | Sticker  | 1        |

  Scenario: Viewing my account orders page
    Given I follow "My orders / my invoices"
    Then I should be on my account orders page

  Scenario Outline: Viewing my orders
    Given I am on my account orders page
     Then I should see "All your orders"
      And I should see 5 orders in the list
      And I should see "<myorder>"
      And I should not see "<order>"
  Examples:
      | myorder    | order    |
      | 000000001  | 000000002  |
      | 000000006  | 000000003  |
      | 000000007  | 000000004  |
      | 000000009  | 000000005  |
      | 000000010  | 000000008  |
      | 000000010  | 000000011  |

  Scenario Outline: Viewing the detail of an order
    Given I am on my account orders page
      And I follow "order-<order>-details"
     Then I should see "Details of your order"
      And I should be on the order show page for <order>
      And I should see <items> items in the list

    Examples:
      | order      | items |
      | 000000001  | 3     |
      | 000000007  | 2     |

  Scenario Outline: Trying to view the detail of an order which is not mine
    Given I go to the order show page for <order>
     Then the response status code should be 403

  Examples:
      | order      |
      | 000000002 |
      | 000000003 |
      | 000000004 |
      | 000000005 |
      | 000000008 |
      | 000000011 |

  Scenario: Tracking an order that has been sent
    Given I am on my account orders page
     Then I should see "Tracking number DTBHH380HG" in the "#order-000000001" element
      And I should see "Shipped" in the "#order-000000001" element

  Scenario Outline: Trying to track an order that has not been sent
    Given I am on my account orders page
     Then I should not see "Tracking number" in the "#order-<order>" element
      And I should see "<state>" in the "#order-<order>" element

  Examples:
      | order     | state       |
      | 000000007 | Ready since |

  Scenario: Tracking an order that has been sent in its details page
    Given I go to the order show page for 000000001
     Then I should see "Tracking number DTBHH380HG" in the "#information" element
      And I should see "Shipped" in the "#information" element

  Scenario Outline: Trying to track an order that has not been sent in its details page
    Given I go to the order show page for <order>
     Then I should not see "Tracking number" in the "#information" element
      And I should see "<state>" in the "#information" element

  Examples:
      | order     | state       |
      | 000000007 | Ready since |

  Scenario: Checking that an invoice is available for an order that has been sent
    Given I am on my account orders page
     Then I should see an "#order-000000001-invoice" element

  Scenario: Checking that an invoice is not available for an order that has not been sent
    Given I am on my account orders page
    Then I should not see an "#order-000000007-invoice" element

  Scenario: Generating an invoice for an order that has been sent
    Given I go to the order invoice page for 000000001
    Then the response status code should be 200

  Scenario: Trying to generate an invoice for an order that has not been sent
    Given I go to the order invoice page for 000000007
    Then the response status code should be 404

  Scenario Outline: Trying to generate an invoice of an order which is not mine
    Given I go to the order invoice page for <order>
    Then the response status code should be 403

  Examples:
    | order     |
    | 000000002 |
    | 000000003 |
    | 000000004 |
    | 000000005 |
    | 000000008 |
    | 000000011 |
