import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export const SITE_NAME = 'بشرة خير';

export const DEFAULT_KEYWORDS = [
    'شقة',
    'شقق',
    'شقة للبيع',
    'شقة للإيجار',
    'بيع شقق',
    'إيجار شقق',
    'ايجار شقة',
    'عقارات',
    'عقار',
    'وحدات سكنية',
    'شقق سكنية',
    'شقة مفروشة',
    'استوديو',
    'شقة للايجار',
    'بيع عقار',
    'إيجار عقار',
    'بشرة خير',
    'عقارات مصر',
    'شقق مصر',
];

const PURPOSE_KEYWORDS = {
    sale: ['شقة للبيع', 'بيع شقة', 'شراء شقة', 'عقار للبيع'],
    rent: ['شقة للإيجار', 'إيجار شقة', 'ايجار شقق', 'شقة مفروشة للإيجار'],
    both: ['شقة للبيع والإيجار', 'بيع وإيجار', 'شقة للبيع أو الإيجار'],
};

export function uniqueKeywords(...groups) {
    return [...new Set(groups.flat().filter(Boolean))].join(', ');
}

export function truncate(text, max = 160) {
    if (!text) {
        return '';
    }

    const normalized = String(text).replace(/\s+/g, ' ').trim();

    if (normalized.length <= max) {
        return normalized;
    }

    return `${normalized.slice(0, max - 1).trim()}…`;
}

export function buildPropertyDescription(property) {
    const parts = [
        property.purpose_label ? `${property.purpose_label} — ${property.name}` : property.name,
        property.location,
        property.rooms ? `${property.rooms} غرف` : null,
        property.bathrooms ? `${property.bathrooms} حمام` : null,
        property.area ? `${property.area} م²` : null,
        property.description,
    ].filter(Boolean);

    return truncate(parts.join(' · '), 160);
}

export function buildPropertyKeywords(property) {
    const purposeWords = PURPOSE_KEYWORDS[property.purpose] || PURPOSE_KEYWORDS.both;

    return uniqueKeywords(
        DEFAULT_KEYWORDS,
        purposeWords,
        ['شقة', property.type?.name, property.city, property.district, property.purpose_label],
        property.rooms ? [`${property.rooms} غرف`] : [],
        property.features?.map((feature) => feature.name) ?? [],
    );
}

export function buildPropertyTitle(property) {
    const purpose = property.purpose_label || 'شقة';
    const location = property.city || property.location;

    if (location) {
        return `${property.name} — ${purpose} في ${location}`;
    }

    return `${property.name} — ${purpose}`;
}

export function buildPropertiesListKeywords(filters = {}) {
    const extra = [];

    if (filters.purpose === 'sale') {
        extra.push(...PURPOSE_KEYWORDS.sale);
    } else if (filters.purpose === 'rent') {
        extra.push(...PURPOSE_KEYWORDS.rent);
    }

    if (filters.city) {
        extra.push(`شقق ${filters.city}`, `شقة ${filters.city}`, `عقارات ${filters.city}`);
    }

    if (filters.status === 'available') {
        extra.push('شقق متاحة', 'شقة متاحة');
    }

    return uniqueKeywords(DEFAULT_KEYWORDS, extra);
}

export function buildPropertiesListTitle(filters = {}) {
    if (filters.purpose === 'sale') {
        return filters.city ? `شقق للبيع في ${filters.city}` : 'شقق للبيع';
    }

    if (filters.purpose === 'rent') {
        return filters.city ? `شقق للإيجار في ${filters.city}` : 'شقق للإيجار';
    }

    if (filters.city) {
        return `شقق في ${filters.city}`;
    }

    return 'جميع الشقق المتاحة';
}

export function buildPropertiesListDescription(filters = {}) {
    if (filters.purpose === 'sale' && filters.city) {
        return `تصفّح شقق للبيع في ${filters.city} مع صور وتفاصيل كاملة. اعثر على شقتك المناسبة وتواصل معنا عبر بشرة خير.`;
    }

    if (filters.purpose === 'rent' && filters.city) {
        return `تصفّح شقق للإيجار في ${filters.city} — وحدات سكنية متنوعة بأسعار وتفاصيل واضحة. تواصل معنا للاستفسار والمعاينة.`;
    }

    if (filters.purpose === 'sale') {
        return 'استكشف شقق للبيع بصور وتفاصيل دقيقة. بيع عقارات ووحدات سكنية مع بشرة خير — تواصل معنا للاستفسار.';
    }

    if (filters.purpose === 'rent') {
        return 'استكشف شقق للإيجار ووحدات سكنية جاهزة. إيجار شقق بمواصفات متنوعة — تصفّح العروض وتواصل معنا.';
    }

    if (filters.city) {
        return `تصفّح الشقق المتاحة في ${filters.city}: بيع وإيجار، صور، مساحة، غرف، ومميزات. اعثر على شقتك المناسبة مع بشرة خير.`;
    }

    return 'تصفّح جميع الشقق المتاحة للبيع والإيجار. صور، تفاصيل، مواصفات، وتواصل مباشر — منصة بشرة خير للعقارات.';
}

export function propertyJsonLd(property, appUrl) {
    const image = property.images?.find((item) => item.media_type !== 'video')?.url
        ?? property.images?.[0]?.url;

    const data = {
        '@context': 'https://schema.org',
        '@type': 'Apartment',
        name: property.name,
        description: buildPropertyDescription(property),
        url: `${appUrl}${route('properties.show', property.id)}`,
    };

    if (property.address || property.city) {
        data.address = {
            '@type': 'PostalAddress',
            addressLocality: property.city || undefined,
            streetAddress: property.address || undefined,
            addressCountry: 'EG',
        };
    }

    if (property.area) {
        data.floorSize = {
            '@type': 'QuantitativeValue',
            value: property.area,
            unitCode: 'MTK',
        };
    }

    if (property.rooms) {
        data.numberOfRooms = property.rooms;
    }

    if (image) {
        data.image = image.startsWith('http') ? image : `${appUrl}${image}`;
    }

    if (property.rent_price || property.price) {
        data.offers = {
            '@type': 'Offer',
            price: property.rent_price || property.price,
            priceCurrency: 'EGP',
            availability: property.status === 'available'
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
        };
    }

    return data;
}

export function organizationJsonLd(appUrl) {
    return {
        '@context': 'https://schema.org',
        '@type': 'RealEstateAgent',
        name: SITE_NAME,
        url: appUrl,
        description: 'منصة عربية لعرض الشقق للبيع والإيجار بتفاصيل وصور واضحة.',
    };
}

export function websiteJsonLd(appUrl) {
    return {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: SITE_NAME,
        url: appUrl,
        inLanguage: 'ar',
        potentialAction: {
            '@type': 'SearchAction',
            target: `${appUrl}/properties?q={search_term_string}`,
            'query-input': 'required name=search_term_string',
        },
    };
}

export function useSeo() {
    const page = usePage();

    const appUrl = computed(() => {
        const configured = page.props.appUrl;

        if (configured) {
            return String(configured).replace(/\/$/, '');
        }

        if (typeof window !== 'undefined') {
            return window.location.origin;
        }

        return '';
    });

    const canonicalUrl = computed(() => `${appUrl.value}${page.url.split('?')[0]}`);

    return {
        appUrl,
        canonicalUrl,
        SITE_NAME,
        DEFAULT_KEYWORDS,
    };
}
