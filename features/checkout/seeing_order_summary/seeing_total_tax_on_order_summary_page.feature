@checkout_with_vat
Feature: Seeing tax total on order summary page
  In order to be certain about tax total
  As a Customer
  I want to be able to see tax total on the order summary page

  Background:
    Given the store operates on a single channel
    And the store operates in "France"
    And the store operates in "Spain"
    And the store has a zone "European Union" with code "EU"
    And this zone has the "France" country member
    And this zone has the "Spain" country member
    And the store has "VAT" tax rate of 20% for "Clothes" within the "EU" zone
    And the store has a product "The Sorting Hat" priced at "$100.00"
    And it belongs to "Clothes" tax category
    And this channel is based in the "Spain" country and allow VAT numbers for the "EU" zone
    And the store ships everywhere for free
    And the store allows paying offline
    And I am a logged in customer

  @api @ui @javascript
  Scenario: Seeing the tax total of 0% when VAT number country is different from the channel base country on order summary page
    Given I have product "The Sorting Hat" in the cart
    When I specify the billing address as "Paris", "1 avenue Notre Dame", "75001", "France" for "Dupont Jean"
    And I specify the billing vat number as "FR10632012100"
    And I complete the addressing step
    And I proceed with "Free" shipping method and "Offline" payment
    Then I should be on the checkout summary step
    And my tax total should be "$0.00"

  @api @ui @javascript
  Scenario: Seeing the tax total of 20% when VAT number country is the same as the channel base country on order summary page
    Given I have product "The Sorting Hat" in the cart
    And this channel is based in the "France" country and allow VAT numbers for the "EU" zone
    When I specify the billing address as "Paris", "1 avenue Notre Dame", "75001", "France" for "Dupont Jean"
    And I specify the billing vat number as "FR10632012100"
    And I complete the addressing step
    And I proceed with "Free" shipping method and "Offline" payment
    Then I should be on the checkout summary step
    And my tax total should be "$20.00"
