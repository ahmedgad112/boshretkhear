<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function __construct(private readonly PropertyService $service) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('properties.view');

        $properties = Property::query()
            ->with(['type', 'images'])
            ->when($request->q, fn ($q, $term) => $q->where(function ($inner) use ($term) {
                $inner->where('name', 'like', '%'.$term.'%')->orWhere('code', 'like', '%'.$term.'%');
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->purpose, fn ($q, $purpose) => $q->where('purpose', $purpose))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(function (Property $property) {
                $image = $property->cardImage();

                return [
                    'id' => $property->id,
                    'code' => $property->code,
                    'name' => $property->name,
                    'type' => $property->type?->name,
                    'purpose_label' => $property->purpose_label,
                    'status' => $property->status,
                    'status_label' => $property->status_label,
                    'city' => $property->city,
                    'price' => $property->price,
                    'rent_price' => $property->rent_price,
                    'image' => $image?->url,
                    'image_type' => $image?->media_type ?? 'image',
                ];
            });

        return Inertia::render('Admin/Properties/Index', [
            'properties' => $properties,
            'filters' => $request->only(['q', 'status', 'purpose']),
        ]);
    }

    public function create(): Response
    {
        $this->requirePermission('properties.create');

        return Inertia::render('Admin/Properties/Form', $this->formData());
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        $this->requirePermission('properties.create');

        $this->service->create(
            $request->validated(),
            $request->file('images', []),
            $request->input('feature_ids', []),
        );

        return redirect()->route('admin.properties.index')->with('success', 'تم إضافة العقار بنجاح.');
    }

    public function show(Property $property): Response
    {
        $this->requirePermission('properties.view');

        $property->load(['type', 'images', 'features', 'bookings.customer', 'sales.customer', 'expenses.category']);

        return Inertia::render('Admin/Properties/Show', [
            'property' => [
                ...$property->toArray(),
                'status_label' => $property->status_label,
                'purpose_label' => $property->purpose_label,
                'rent_period_label' => $property->rent_period_label,
                'images' => $property->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'media_type' => $img->media_type ?? 'image',
                    'is_primary' => $img->is_primary,
                    'sort_order' => $img->sort_order,
                ]),
            ],
        ]);
    }

    public function edit(Property $property): Response
    {
        $this->requirePermission('properties.update');

        $property->load(['images', 'features']);

        return Inertia::render('Admin/Properties/Form', [
            ...$this->formData(),
            'property' => [
                ...$property->toArray(),
                'feature_ids' => $property->features->pluck('id'),
                'images' => $property->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'media_type' => $img->media_type ?? 'image',
                    'is_primary' => $img->is_primary,
                ]),
            ],
        ]);
    }

    public function update(PropertyRequest $request, Property $property): RedirectResponse
    {
        $this->requirePermission('properties.update');

        $this->service->update(
            $property,
            $request->validated(),
            $request->file('images', []),
            $request->input('feature_ids', []),
        );

        return redirect()->route('admin.properties.index')->with('success', 'تم تحديث بيانات العقار بنجاح.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->requirePermission('properties.delete');
        $this->service->delete($property);

        return back()->with('success', 'تم حذف العقار بنجاح.');
    }

    public function changeStatus(Request $request, Property $property): RedirectResponse
    {
        $this->requirePermission('properties.change_status');
        $request->validate(['status' => 'required|in:available,reserved,rented,sold,unavailable']);
        $this->service->changeStatus($property, $request->string('status'));

        return back()->with('success', 'تم تغيير حالة العقار بنجاح.');
    }

    public function destroyImage(Property $property, PropertyImage $image): RedirectResponse
    {
        $this->requirePermission('properties.update');
        $this->service->deleteImage($property, $image->id);

        return back()->with('success', 'تم حذف الصورة بنجاح.');
    }

    public function setPrimaryImage(Property $property, PropertyImage $image): RedirectResponse
    {
        $this->requirePermission('properties.update');
        $this->service->setPrimaryImage($property, $image->id);

        return back()->with('success', 'تم تحديد الصورة الرئيسية بنجاح.');
    }

    public function reorderImages(Request $request, Property $property): RedirectResponse
    {
        $this->requirePermission('properties.update');
        $request->validate(['ordered_ids' => 'required|array']);
        $this->service->reorderImages($property, $request->input('ordered_ids', []));

        return back()->with('success', 'تم تحديث ترتيب الصور.');
    }

    private function formData(): array
    {
        return [
            'types' => PropertyType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'features' => PropertyFeature::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
