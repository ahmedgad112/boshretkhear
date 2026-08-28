<?php

namespace App\Services;

use App\Models\Expense;
use App\Support\CodeGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function __construct(
        private readonly ActivityLogger $logger,
        private readonly FinancialService $financial,
    ) {}

    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::query()->create([
                'code' => CodeGenerator::next('مصروف-', Expense::class),
                'expense_category_id' => $data['expense_category_id'],
                'amount' => $data['amount'],
                'expense_date' => $data['expense_date'],
                'property_id' => $data['property_id'] ?? null,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->financial->record(
                'expense',
                (float) $expense->amount,
                $expense->expense_date->toDateString(),
                $expense->description ?: 'مصروف '.$expense->code,
                $expense,
                $expense->property_id,
                null,
                $expense->notes,
            );

            $this->logger->log('expense.created', 'تم إضافة مصروف: '.$expense->code, $expense, [
                'المبلغ' => $expense->amount,
            ]);

            return $expense->fresh(['category', 'property', 'creator']);
        });
    }

    public function update(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            $expense->update([
                'expense_category_id' => $data['expense_category_id'],
                'amount' => $data['amount'],
                'expense_date' => $data['expense_date'],
                'property_id' => $data['property_id'] ?? null,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->financial->deleteForReference($expense);
            $this->financial->record(
                'expense',
                (float) $expense->amount,
                $expense->expense_date->toDateString(),
                $expense->description ?: 'مصروف '.$expense->code,
                $expense,
                $expense->property_id,
                null,
                $expense->notes,
            );

            $this->logger->log('expense.updated', 'تم تعديل المصروف: '.$expense->code, $expense);

            return $expense->fresh(['category', 'property', 'creator']);
        });
    }

    public function delete(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $this->financial->deleteForReference($expense);
            $expense->delete();
            $this->logger->log('expense.deleted', 'تم حذف المصروف: '.$expense->code, $expense);
        });
    }
}
