<?php

namespace App\Services;

use App\Models\Property;
use App\Support\CodeGenerator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PropertyService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data, array $images = [], array $featureIds = []): Property
    {
        return DB::transaction(function () use ($data, $images, $featureIds) {
            $property = Property::query()->create([
                ...$this->payload($data),
                'code' => $data['code'] ?: CodeGenerator::next('عقار-', Property::class),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $property->features()->sync($featureIds);
            $this->storeImages($property, $images);
            $this->logger->log('property.created', 'تم إضافة عقار: '.$property->name, $property);

            return $property->fresh(['type', 'images', 'features']);
        });
    }

    public function update(Property $property, array $data, array $images = [], array $featureIds = []): Property
    {
        return DB::transaction(function () use ($property, $data, $images, $featureIds) {
            $property->update([
                ...$this->payload($data),
                'code' => $data['code'] ?: $property->code,
                'updated_by' => Auth::id(),
            ]);

            $property->features()->sync($featureIds);
            $this->storeImages($property, $images);
            $this->logger->log('property.updated', 'تم تعديل العقار: '.$property->name, $property);

            return $property->fresh(['type', 'images', 'features']);
        });
    }

    public function delete(Property $property): void
    {
        $property->delete();
        $this->logger->log('property.deleted', 'تم حذف العقار: '.$property->name, $property);
    }

    public function changeStatus(Property $property, string $status): Property
    {
        $property->update([
            'status' => $status,
            'updated_by' => Auth::id(),
        ]);

        $this->logger->log('property.status', 'تم تغيير حالة العقار '.$property->name, $property, [
            'الحالة' => $status,
        ]);

        return $property->fresh();
    }

    public function deleteImage(Property $property, int $imageId): void
    {
        $image = $property->images()->where('id', $imageId)->firstOrFail();
        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = $property->images()->first();
            $next?->update(['is_primary' => true]);
        }
    }

    public function setPrimaryImage(Property $property, int $imageId): void
    {
        $image = $property->images()->where('id', $imageId)->firstOrFail();

        if ($image->isVideo()) {
            throw ValidationException::withMessages([
                'images' => 'لا يمكن تعيين فيديو كصورة رئيسية.',
            ]);
        }

        $property->images()->update(['is_primary' => false]);
        $property->images()->where('id', $imageId)->update(['is_primary' => true]);
    }

    public function reorderImages(Property $property, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            $property->images()->where('id', $id)->update(['sort_order' => $index]);
        }
    }

    private function payload(array $data): array
    {
        return [
            'name' => $data['name'],
            'property_type_id' => $data['property_type_id'],
            'purpose' => $data['purpose'],
            'price' => $data['price'] ?? null,
            'rent_price' => $data['rent_price'] ?? null,
            'rent_period' => $data['rent_period'] ?? null,
            'district' => $data['district'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'area' => $data['area'] ?? null,
            'rooms' => $data['rooms'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'floors' => $data['floors'] ?? null,
            'floor_number' => $data['floor_number'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'available',
            'notes' => $data['notes'] ?? null,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'is_published' => (bool) ($data['is_published'] ?? true),
        ];
    }

    /**
     * @param  array<int, UploadedFile>  $images
     */
    private function storeImages(Property $property, array $images): void
    {
        if ($images === []) {
            return;
        }

        $allowedImages = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedVideos = ['video/mp4', 'video/webm', 'video/quicktime'];
        $maxImageBytes = 5 * 1024 * 1024;
        $maxVideoBytes = 50 * 1024 * 1024;
        $sort = (int) $property->images()->max('sort_order');
        $hasPrimary = $property->images()->where('is_primary', true)->exists();

        foreach ($images as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $mime = $file->getMimeType() ?? '';
            $isVideo = in_array($mime, $allowedVideos, true);
            $isImage = in_array($mime, $allowedImages, true);

            if (! $isVideo && ! $isImage) {
                throw ValidationException::withMessages([
                    'images' => 'يُسمح فقط بصور (JPG, PNG, WEBP, GIF) أو فيديو (MP4, WEBM, MOV).',
                ]);
            }

            if ($isImage && $file->getSize() > $maxImageBytes) {
                throw ValidationException::withMessages([
                    'images' => 'حجم الصورة يجب ألا يتجاوز خمسة ميغابايت.',
                ]);
            }

            if ($isVideo && $file->getSize() > $maxVideoBytes) {
                throw ValidationException::withMessages([
                    'images' => 'حجم الفيديو يجب ألا يتجاوز 50 ميغابايت.',
                ]);
            }

            $name = CodeGenerator::uniqueFileName($file->getClientOriginalName());
            $path = $file->storeAs('properties/'.$property->id, $name, 'public');

            $property->images()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'media_type' => $isVideo ? 'video' : 'image',
                'is_primary' => ! $hasPrimary && $isImage && $index === 0,
                'sort_order' => ++$sort,
            ]);
        }
    }
}
