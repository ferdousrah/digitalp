<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'expense_category_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'reference',
        'payment_method',
        'paid_to',
        'receipt_path',
        'is_recurring',
        'recurrence_period',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'expense_date' => 'date',
            'is_recurring' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function paymentMethods(): array
    {
        return [
            'cash'         => 'Cash',
            'bank'         => 'Bank Transfer',
            'bkash'        => 'bKash',
            'nagad'        => 'Nagad',
            'rocket'       => 'Rocket',
            'card'         => 'Credit/Debit Card',
            'cheque'       => 'Cheque',
            'online'       => 'Online Payment',
            'other'        => 'Other',
        ];
    }

    public static function recurrencePeriods(): array
    {
        return [
            'weekly'    => 'Weekly',
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly'    => 'Yearly',
        ];
    }
}
