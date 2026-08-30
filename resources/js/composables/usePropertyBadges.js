const availableLabels = {
    rent: 'متاح للإيجار',
    sale: 'متاح للبيع',
    both: 'بيع وإيجار',
};

export function getPropertyDisplayBadge(property) {
    const { purpose, status, purpose_label, status_label } = property;

    if (status === 'available') {
        return {
            kind: 'purpose',
            value: 'available',
            label: availableLabels[purpose] || purpose_label,
        };
    }

    return {
        kind: 'status',
        value: status,
        label: status_label,
    };
}
