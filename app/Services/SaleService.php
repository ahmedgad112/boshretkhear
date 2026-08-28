<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\CodeGenerator;

class SaleService
{
    public function __construct(
        private readonly ActivityLogger $logger,
        private readonly PaymentService $payments,
        private readonly FinancialService $financial,
    ) {}

    public function calculate(array $data): array
    {
        $price = (float) $data['sale_price'];
        $discount = (float) ($data['discount'] ?? 0);
        $final = $price - $discount;

        if ($final < 0) {
            throw ValidationException::withMessages([
                'discount' => 'الخصم أكبر من سعر البيع.',
            ]);
        }

        return [
            'final_price' => round($final, 2),
        ];
    }

    public function create(array $data): Sale
    {
        $calc = $this->calculate($data);

        return DB::transaction(function () use ($data, $calc) {
            $sale = Sale::query()->create([
                'code' => CodeGenerator::next('بيع-', Sale::class),
                'property_id' => $data['property_id'],
                'customer_id' => $data['customer_id'],
                'sale_price' => $data['sale_price'],
                'discount' => $data['discount'] ?? 0,
                'final_price' => $calc['final_price'],
                'paid_amount' => 0,
                'remaining_amount' => $calc['final_price'],
                'sale_date' => $data['sale_date'],
                'payment_method' => $data['payment_method'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            if (($data['status'] ?? 'pending') === 'completed') {
                $sale->property?->update(['status' => 'sold']);
                $this->financial->record(
                    'sale_income',
                    (float) $sale->final_price,
                    $sale->sale_date->toDateString(),
                    'إيراد بيع العقار '.$sale->property?->name,
                    $sale,
                    $sale->property_id,
                    $sale->customer_id,
                );
            }

            if (! empty($data['initial_payment']) && (float) $data['initial_payment'] > 0) {
                $this->payments->recordSalePayment($sale->fresh(), [
                    'amount' => $data['initial_payment'],
                    'paid_at' => $data['sale_date'],
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'notes' => 'دفعة عند إنشاء عملية البيع',
                ]);
            }

            $this->logger->log('sale.created', 'تم إنشاء عملية بيع: '.$sale->code, $sale);

            return $sale->fresh(['property', 'customer', 'payments']);
        });
    }

    public function update(Sale $sale, array $data): Sale
    {
        $calc = $this->calculate($data);

        return DB::transaction(function () use ($sale, $data, $calc) {
            $wasCompleted = $sale->status === 'completed';

            $sale->update([
                'property_id' => $data['property_id'],
                'customer_id' => $data['customer_id'],
                'sale_price' => $data['sale_price'],
                'discount' => $data['discount'] ?? 0,
                'final_price' => $calc['final_price'],
                'remaining_amount' => max(0, $calc['final_price'] - (float) $sale->paid_amount),
                'sale_date' => $data['sale_date'],
                'payment_method' => $data['payment_method'] ?? $sale->payment_method,
                'status' => $data['status'] ?? $sale->status,
                'notes' => $data['notes'] ?? $sale->notes,
                'updated_by' => Auth::id(),
            ]);

            if ($sale->status === 'completed') {
                $sale->property?->update(['status' => 'sold']);

                if (! $wasCompleted) {
                    $this->financial->record(
                        'sale_income',
                        (float) $sale->final_price,
                        $sale->sale_date->toDateString(),
                        'إيراد بيع العقار '.$sale->property?->name,
                        $sale,
                        $sale->property_id,
                        $sale->customer_id,
                    );
                }
            }

            $this->logger->log('sale.updated', 'تم تعديل عملية البيع: '.$sale->code, $sale);

            return $sale->fresh(['property', 'customer', 'payments']);
        });
    }

    public function complete(Sale $sale): Sale
    {
        if ($sale->status === 'completed') {
            return $sale;
        }

        return $this->update($sale, [
            'property_id' => $sale->property_id,
            'customer_id' => $sale->customer_id,
            'sale_price' => $sale->sale_price,
            'discount' => $sale->discount,
            'sale_date' => $sale->sale_date->toDateString(),
            'payment_method' => $sale->payment_method,
            'status' => 'completed',
            'notes' => $sale->notes,
        ]);
    }

    public function delete(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $property = $sale->property;
            $this->financial->deleteForReference($sale);
            $sale->delete();

            if ($property && $property->status === 'sold') {
                $property->update(['status' => 'available']);
            }

            $this->logger->log('sale.deleted', 'تم حذف عملية البيع: '.$sale->code, $sale);
        });
    }
}
