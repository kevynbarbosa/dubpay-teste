<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case CREATED = 'created';
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
