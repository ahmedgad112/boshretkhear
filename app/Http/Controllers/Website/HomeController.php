<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $base = Property::query()->published()->with(['type', 'images', 'features']);

        return Inertia::render('Public/Home', [
            'featured' => (clone $base)->featured()->latest()->limit(6)->get()->map(fn ($p) => $this->card($p)),
            'forSale' => (clone $base)->forSale()->where('status', 'available')->latest()->limit(6)->get()->map(fn ($p) => $this->card($p)),
            'forRent' => (clone $base)->forRent()->whereIn('status', ['available', 'reserved'])->latest()->limit(6)->get()->map(fn ($p) => $this->card($p)),
            'types' => PropertyType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'cities' => Property::query()->published()->whereNotNull('city')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function properties(Request $request): Response
    {
        $query = Property::query()->published()->with(['type', 'images', 'features']);

        if ($request->filled('purpose')) {
            if ($request->purpose === 'sale') {
                $query->forSale();
            } elseif ($request->purpose === 'rent') {
                $query->forRent();
            }
        }

        $query
            ->when($request->property_type_id, fn ($q, $id) => $q->where('property_type_id', $id))
            ->when($request->city, fn ($q, $city) => $q->where('city', $city))
            ->when($request->district, fn ($q, $district) => $q->where('district', 'like', '%'.$district.'%'))
            ->when($request->price_from, fn ($q, $value) => $q->where(function ($inner) use ($value) {
                $inner->where('price', '>=', $value)->orWhere('rent_price', '>=', $value);
            }))
            ->when($request->price_to, fn ($q, $value) => $q->where(function ($inner) use ($value) {
                $inner->where('price', '<=', $value)->orWhere('rent_price', '<=', $value);
            }))
            ->when($request->area_from, fn ($q, $value) => $q->where('area', '>=', $value))
            ->when($request->area_to, fn ($q, $value) => $q->where('area', '<=', $value))
            ->when($request->rooms, fn ($q, $value) => $q->where('rooms', '>=', $value))
            ->when($request->bathrooms, fn ($q, $value) => $q->where('bathrooms', '>=', $value))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, fn ($q, $term) => $q->where(function ($inner) use ($term) {
                $inner->where('name', 'like', '%'.$term.'%')
                    ->orWhere('city', 'like', '%'.$term.'%')
                    ->orWhere('district', 'like', '%'.$term.'%')
                    ->orWhere('address', 'like', '%'.$term.'%');
            }));

        $paginated = $query->latest()->paginate(12)->withQueryString();

        return Inertia::render('Public/Properties', [
            'properties' => $paginated->through(fn ($p) => $this->card($p)),
            'filters' => $request->only([
                'q', 'purpose', 'property_type_id', 'city', 'district',
                'price_from', 'price_to', 'area_from', 'area_to', 'rooms', 'bathrooms', 'status',
            ]),
            'types' => PropertyType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'cities' => Property::query()->published()->whereNotNull('city')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(Property $property): Response
    {
        abort_unless($property->is_published, 404);

        $property->load(['type', 'images', 'features']);

        $similar = Property::query()
            ->published()
            ->where('id', '!=', $property->id)
            ->where(function ($q) use ($property) {
                $q->where('property_type_id', $property->property_type_id)
                    ->orWhere('city', $property->city);
            })
            ->with(['type', 'images'])
            ->limit(3)
            ->get()
            ->map(fn ($p) => $this->card($p));

        return Inertia::render('Public/PropertyShow', [
            'property' => [
                ...$property->toArray(),
                'status_label' => $property->status_label,
                'purpose_label' => $property->purpose_label,
                'rent_period_label' => $property->rent_period_label,
                'location' => $property->location,
                'images' => $property->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'media_type' => $img->media_type ?? 'image',
                    'is_primary' => $img->is_primary,
                ]),
                'contact_phone' => Setting::getValue('phone'),
                'contact_email' => Setting::getValue('email'),
            ],
            'similar' => $similar,
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Public/About');
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function storeInquiry(InquiryRequest $request, ActivityLogger $logger): RedirectResponse
    {
        $inquiry = Inquiry::query()->create($request->validated());
        $logger->log('inquiry.created', 'تم استلام طلب تواصل من '.$inquiry->name, $inquiry);

        return back()->with('success', 'تم إرسال طلبك بنجاح. سنتواصل معك قريبًا.');
    }

    private function card(Property $property): array
    {
        $primary = $property->cardImage();

        return [
            'id' => $property->id,
            'name' => $property->name,
            'code' => $property->code,
            'type' => $property->type?->name,
            'purpose' => $property->purpose,
            'purpose_label' => $property->purpose_label,
            'status' => $property->status,
            'status_label' => $property->status_label,
            'city' => $property->city,
            'district' => $property->district,
            'location' => $property->location,
            'area' => $property->area,
            'rooms' => $property->rooms,
            'bathrooms' => $property->bathrooms,
            'price' => $property->price,
            'rent_price' => $property->rent_price,
            'rent_period_label' => $property->rent_period_label,
            'image' => $primary?->url,
            'image_type' => $primary?->media_type ?? 'image',
        ];
    }
}
