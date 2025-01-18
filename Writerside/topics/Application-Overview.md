# Application Overview

The application can track all the incomes, expenses, along with internal transfers within accounts as well.

## Accounts

The accounts section holds the details for all your accounts. It can be your savings, current accounts in banks,
credit cards, balance in a trading account, or cash balance in hand.

All of these accounts can be stored under their different account types as listed below:

| Account Type | Balance Type | Feature                                                            |
|--------------|--------------|--------------------------------------------------------------------|
| savings      | +            | Any savings bank account which earns interest at regular frequency |
| current      | +            | An account which is in bank but does not earn any interest         |
| credit       | -            | This represents a credit card, which is negative in nature         |
| loan         | -            | This represents a loan account, which is negative in nature        |
| trading      | +            | Trading account associated with demat                              |
| cash         | +            | As the name suggests                                               |

_Do not use any of these accounts for adding any other asset class as this structure is only meant to track liquid cash points.
Any asset class like real estate, gold, equity, etc are highly volatile in nature, and there will be a separate way to track that in the future, to assess current net worth._


## People

Any kind of [income](#incomes) or [expense](#expenses) has one creditor and one debtor. 
[Account](#accounts) is mapped on one side and a person is mapped on another. This is optional though.
It can be useful to track to whom a payment is made or from whom we received any payment when using filters.

## Categories

Categories are a single group head to club any incomes or expenses. 
This helps us classify the incomes or expenditures for a particular group.

## Incomes

This holds all the incomes.

## Expenses

This holds all the expenses

## Transfers

This holds all the internal transfer of money between any two accounts.

## Tags

Tags can be used like tags on incomes, expenses or transfers.
You can define any color to that tag which will be later on used in views/reports.

## Balances

This section tracks account balances periodically, like `monthly` and `yearly` with an `inital` entry as the exception.
This `initial` entry denotes the balance as per the account's `initial_date` entry.
