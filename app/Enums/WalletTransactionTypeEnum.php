<?php

namespace App\Enums;

enum WalletTransactionTypeEnum: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}