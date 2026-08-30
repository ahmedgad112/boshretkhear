import { ref } from 'vue';

export function useShare() {
    const copied = ref(false);
    let timeoutId;

    async function share({ title, text, url }) {
        if (navigator.share) {
            try {
                await navigator.share({ title, text, url });
                return { method: 'native' };
            } catch (error) {
                if (error?.name === 'AbortError') {
                    return { method: 'cancelled' };
                }
            }
        }

        try {
            await navigator.clipboard.writeText(url);
            copied.value = true;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                copied.value = false;
            }, 2000);
            return { method: 'clipboard' };
        } catch {
            window.prompt('انسخ الرابط:', url);
            return { method: 'prompt' };
        }
    }

    function propertyShareUrl(property) {
        return `${window.location.origin}${route('properties.show', property.id)}`;
    }

    async function shareProperty(property) {
        return share({
            title: property.name,
            text: [property.name, property.location, property.purpose_label].filter(Boolean).join(' - '),
            url: propertyShareUrl(property),
        });
    }

    return { copied, share, shareProperty, propertyShareUrl };
}
