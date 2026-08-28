<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyTypeRequest;
use App\Models\PropertyType;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PropertyTypeController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function index(): Response
    {
        $this->requirePermission('property_types.view');

        return Inertia::render('Admin/PropertyTypes/Index', [
            'types' => PropertyType::query()->withCount('properties')->latest()->get(),
        ]);
    }

    public function store(PropertyTypeRequest $request): RedirectResponse
    {
        $this->requirePermission('property_types.create');

        $type = PropertyType::query()->create([
            ...$request->validated(),
            'slug' => (Str::slug($request->name) ?: 'type').'-'.Str::lower(Str::random(4)),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->logger->log('property_type.created', 'تم إضافة نوع عقار: '.$type->name, $type);

        return back()->with('success', 'تم إضافة نوع العقار بنجاح.');
    }

    public function update(PropertyTypeRequest $request, PropertyType $propertyType): RedirectResponse
    {
        $this->requirePermission('property_types.update');

        $propertyType->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->logger->log('property_type.updated', 'تم تعديل نوع العقار: '.$propertyType->name, $propertyType);

        return back()->with('success', 'تم تحديث نوع العقار بنجاح.');
    }

    public function destroy(PropertyType $propertyType): RedirectResponse
    {
        $this->requirePermission('property_types.delete');

        if ($propertyType->properties()->exists()) {
            return back()->with('error', 'لا يمكن حذف نوع مرتبط بعقارات.');
        }

        $propertyType->delete();
        $this->logger->log('property_type.deleted', 'تم حذف نوع العقار: '.$propertyType->name, $propertyType);

        return back()->with('success', 'تم حذف نوع العقار بنجاح.');
    }
}
