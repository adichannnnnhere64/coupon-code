// src/routes/_public.tsx
import { createFileRoute, Outlet, redirect } from '@tanstack/react-router'
import { useAuthStore } from '../stores/useAuthStore'

export const Route = createFileRoute('/_public')({
    beforeLoad: ({ context }) => {
        if (useAuthStore.getState().isAuthenticated) {
            throw redirect({ to: '/dashboard' })
        }
    },

    component: () => (
        <div>
            <Outlet />
        </div>
    ),
})
