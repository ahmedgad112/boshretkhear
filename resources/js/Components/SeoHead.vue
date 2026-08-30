<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { SITE_NAME, truncate, uniqueKeywords, useSeo } from '../composables/useSeo';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    keywords: {
        type: [String, Array],
        default: () => [],
    },
    image: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'website',
    },
    robots: {
        type: String,
        default: 'index, follow',
    },
    jsonLd: {
        type: [Object, Array],
        default: null,
    },
});

const { appUrl, canonicalUrl } = useSeo();

const metaDescription = computed(() => truncate(props.description, 160));

const metaKeywords = computed(() => {
    if (Array.isArray(props.keywords)) {
        return uniqueKeywords(props.keywords);
    }

    return props.keywords;
});

const ogImage = computed(() => {
    if (!props.image) {
        return `${appUrl.value}/favicon.svg`;
    }

    return props.image.startsWith('http') ? props.image : `${appUrl.value}${props.image}`;
});

const structuredData = computed(() => {
    if (!props.jsonLd) {
        return null;
    }

    const payload = Array.isArray(props.jsonLd) ? props.jsonLd : [props.jsonLd];

    return JSON.stringify(payload.length === 1 ? payload[0] : payload);
});
</script>

<template>
    <Head :title="title">
        <meta head-key="description" name="description" :content="metaDescription" />
        <meta head-key="keywords" name="keywords" :content="metaKeywords" />
        <meta head-key="robots" name="robots" :content="robots" />
        <link head-key="canonical" rel="canonical" :href="canonicalUrl" />

        <meta head-key="og:type" property="og:type" :content="type" />
        <meta head-key="og:title" property="og:title" :content="`${title} | ${SITE_NAME}`" />
        <meta head-key="og:description" property="og:description" :content="metaDescription" />
        <meta head-key="og:url" property="og:url" :content="canonicalUrl" />
        <meta head-key="og:site_name" property="og:site_name" :content="SITE_NAME" />
        <meta head-key="og:locale" property="og:locale" content="ar_EG" />
        <meta head-key="og:image" property="og:image" :content="ogImage" />

        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:title" name="twitter:title" :content="`${title} | ${SITE_NAME}`" />
        <meta head-key="twitter:description" name="twitter:description" :content="metaDescription" />
        <meta head-key="twitter:image" name="twitter:image" :content="ogImage" />

        <component
            v-if="structuredData"
            :is="'script'"
            head-key="json-ld"
            type="application/ld+json"
            v-text="structuredData"
        />
    </Head>
</template>
