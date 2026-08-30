<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $staticPages = [
            ['loc' => $baseUrl.'/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $baseUrl.route('properties.index', [], false), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => $baseUrl.route('properties.sale', [], false), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => $baseUrl.route('properties.rent', [], false), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => $baseUrl.route('about', [], false), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl.route('contact', [], false), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        $properties = Property::query()
            ->published()
            ->apartments()
            ->latest('updated_at')
            ->get(['id', 'updated_at']);

        $xml = view('sitemap', [
            'staticPages' => $staticPages,
            'properties' => $properties,
            'baseUrl' => $baseUrl,
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
