<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Property;
use App\Models\User;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reports,
        private readonly ExportService $export,
    ) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('reports.view');

        $type = $request->string('type', 'properties')->toString();
        $filters = $request->only(['from', 'to', 'property_id', 'customer_id', 'type', 'user_id']);
        $rows = $this->resolve($type, $filters);

        return Inertia::render('Admin/Reports/Index', [
            'type' => $type,
            'rows' => $rows,
            'filters' => $filters,
            'properties' => Property::query()->orderBy('name')->get(['id', 'name']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request)
    {
        $this->requirePermission('reports.export');

        $type = $request->string('type', 'properties')->toString();
        $format = $request->string('format', 'xlsx')->toString();
        $filters = $request->only(['from', 'to', 'property_id', 'customer_id', 'type', 'user_id']);
        $rows = collect($this->resolve($type, $filters));
        $title = $this->titles()[$type] ?? 'تقرير';
        $filename = $title.'-'.now()->format('YmdHis').($format === 'pdf' ? '.pdf' : '.xlsx');

        return $format === 'pdf'
            ? $this->export->pdf($title, $rows, $filename)
            : $this->export->excel($title, $rows, $filename);
    }

    private function resolve(string $type, array $filters): array
    {
        return match ($type) {
            'bookings' => $this->reports->bookings($filters)->all(),
            'sales' => $this->reports->sales($filters)->all(),
            'customers' => $this->reports->customers($filters)->all(),
            'payments' => $this->reports->payments($filters)->all(),
            'expenses' => $this->reports->expenses($filters)->all(),
            'revenues' => $this->reports->revenues($filters)->all(),
            'profits' => $this->reports->profits($filters),
            'dues' => $this->reports->dues($filters)->all(),
            default => $this->reports->properties($filters)->all(),
        };
    }

    private function titles(): array
    {
        return [
            'properties' => 'تقرير العقارات',
            'bookings' => 'تقرير الإيجارات',
            'sales' => 'تقرير المبيعات',
            'customers' => 'تقرير العملاء',
            'payments' => 'تقرير المدفوعات',
            'expenses' => 'تقرير المصروفات',
            'revenues' => 'تقرير الإيرادات',
            'profits' => 'تقرير صافي الأرباح',
            'dues' => 'تقرير المبالغ المستحقة',
        ];
    }
}
