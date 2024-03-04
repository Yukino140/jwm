<?php


// src/Enum/AccountType.php

namespace App\Entity\enum;

enum AccountType: string
{
    // Tailored for students, offering features like no minimum balance, no monthly fees, and sometimes perks or discounts.
    case STUDENT = 'STUDENT';
    // Designed for daily transactions, such as deposits, withdrawals, and direct payments. Often comes with a debit card and checkbook.
    case CHECKING = 'CHECKING';
   // Aimed at helping customers save money. Typically offers interest on the deposited amount but might have limitations on the number of transactions.
    case SAVINGS = 'SAVINGS';
    // Owned by two or more individuals, typically used by couples or business partners. All owners have equal access to the account.
    case JOINT = 'JOINT';
    // Designed for business transactions, allowing companies to manage their finances, employee salaries, and other financial operations separately from personal accounts.
    case BUSINESS = 'BUSINESS';



}
