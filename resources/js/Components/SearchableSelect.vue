<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    items: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'ابحث...' },
    emptyText: { type: String, default: 'لا توجد نتائج' },
    label: { type: String, default: '' },
    required: { type: Boolean, default: false },
    getLabel: { type: Function, default: (item) => item.name },
    getSubLabel: { type: Function, default: null },
    getSearchText: { type: Function, default: null },
});

const emit = defineEmits(['update:modelValue', 'select']);

const open = ref(false);
const query = ref('');
const root = ref(null);
const input = ref(null);

const selected = computed(() => props.items.find((item) => String(item.id) === String(props.modelValue)) || null);

const filtered = computed(() => {
    const term = query.value.trim().toLowerCase();

    if (!term) {
        return props.items.slice(0, 50);
    }

    return props.items.filter((item) => {
        const haystack = (props.getSearchText ? props.getSearchText(item) : props.getLabel(item)).toLowerCase();

        return haystack.includes(term);
    }).slice(0, 50);
});

const syncQueryFromSelection = () => {
    if (selected.value) {
        query.value = props.getLabel(selected.value);
    } else if (!open.value) {
        query.value = '';
    }
};

watch(() => props.modelValue, syncQueryFromSelection, { immediate: true });
watch(() => props.items, syncQueryFromSelection);

const openDropdown = async () => {
    open.value = true;
    await nextTick();
    input.value?.focus();
};

const closeDropdown = () => {
    open.value = false;
    syncQueryFromSelection();
};

const selectItem = (item) => {
    emit('update:modelValue', item.id);
    emit('select', item);
    query.value = props.getLabel(item);
    open.value = false;
};

const clearSelection = () => {
    emit('update:modelValue', '');
    emit('select', null);
    query.value = '';
    open.value = true;
    nextTick(() => input.value?.focus());
};

const onInput = () => {
    open.value = true;

    if (selected.value && query.value !== props.getLabel(selected.value)) {
        emit('update:modelValue', '');
        emit('select', null);
    }
};

const onClickOutside = (event) => {
    if (root.value && !root.value.contains(event.target)) {
        closeDropdown();
    }
};

onMounted(() => document.addEventListener('click', onClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside));
</script>

<template>
    <div ref="root" class="relative">
        <label v-if="label" class="ui-label">{{ label }}</label>
        <div class="relative">
            <input
                ref="input"
                v-model="query"
                type="search"
                :placeholder="placeholder"
                :required="required && !modelValue"
                autocomplete="off"
                class="ui-input pe-10"
                @focus="openDropdown"
                @input="onInput"
                @keydown.escape.prevent="closeDropdown"
                @keydown.enter.prevent
            />
            <button
                v-if="modelValue"
                type="button"
                class="absolute inset-y-0 end-0 flex items-center px-3 text-slate-400 hover:text-slate-700"
                @click="clearSelection"
            >
                ✕
            </button>
        </div>

        <div
            v-if="open"
            class="absolute z-30 mt-1 max-h-56 w-full overflow-auto rounded-xl border border-slate-200 bg-white shadow-lg"
        >
            <button
                v-for="item in filtered"
                :key="item.id"
                type="button"
                class="flex w-full flex-col gap-0.5 border-b border-slate-100 px-3 py-2.5 text-right text-sm last:border-0 hover:bg-emerald-50"
                :class="String(item.id) === String(modelValue) ? 'bg-emerald-50 text-forest font-bold' : 'text-slate-700'"
                @click="selectItem(item)"
            >
                <span>{{ getLabel(item) }}</span>
                <span v-if="getSubLabel?.(item)" class="text-xs font-medium text-slate-500">{{ getSubLabel(item) }}</span>
            </button>
            <p v-if="!filtered.length" class="px-3 py-4 text-center text-sm text-slate-500">{{ emptyText }}</p>
        </div>
    </div>
</template>
