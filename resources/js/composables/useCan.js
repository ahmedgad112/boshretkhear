import { usePage } from '@inertiajs/vue3';

export function useCan() {
    const page = usePage();

    const can = (permission) => {
        const permissions = Array.isArray(page.props.auth?.user?.permissions)
            ? page.props.auth.user.permissions
            : Object.values(page.props.auth?.user?.permissions || {});
        const roles = Array.isArray(page.props.auth?.user?.roles)
            ? page.props.auth.user.roles
            : Object.values(page.props.auth?.user?.roles || {});

        return roles.includes('المدير العام') || permissions.includes(permission);
    };

    return { can };
}
