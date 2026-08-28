<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use App\Support\CodeGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FinancialService
{
    public function record(
        string $type,
        float $amount,
        string $date,
        string $description,
        ?Model $reference = null,
        ?int $propertyId = null,
        ?int $customerId = null,
        ?string $notes = null,
    ): FinancialTransaction {
        return FinancialTransaction::query()->create([
            'code' => CodeGenerator::next('مالية-', FinancialTransaction::class),
            'type' => $type,
            'amount' => $amount,
            'transaction_date' => $date,
            'property_id' => $propertyId,
            'customer_id' => $customerId,
            'user_id' => Auth::id(),
            'description' => $description,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
            'notes' => $notes,
        ]);
    }

    public function deleteForReference(Model $reference): void
    {
        FinancialTransaction::query()
            ->where('reference_type', $reference::class)
            ->where('reference_id', $reference->getKey())
            ->delete();
    }

    public function summary(?string $from = null, ?string $to = null, ?int $propertyId = null): array
    {
        $query = FinancialTransaction::query();

        if ($from) {
            $query->whereDate('transaction_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('transaction_date', '<=', $to);
        }

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        $incomeTypes = ['rent_income', 'sale_income', 'customer_payment'];
        $expenseTypes = ['expense', 'refund'];

        $revenues = (clone $query)->whereIn('type', $incomeTypes)->sum('amount');
        $expenses = (clone $query)->whereIn('type', $expenseTypes)->sum('amount');
        $rentIncome = (clone $query)->where('type', 'rent_income')->sum('amount');
        $saleIncome = (clone $query)->where('type', 'sale_income')->sum('amount');
        $customerPayments = (clone $query)->where('type', 'customer_payment')->sum('amount');

        return [
            'revenues' => (float) $revenues,
            'expenses' => (float) $expenses,
            'net' => (float) $revenues - (float) $expenses,
            'rent_income' => (float) $rentIncome,
            'sale_income' => (float) $saleIncome,
            'customer_payments' => (float) $customerPayments,
        ];
    }
}
