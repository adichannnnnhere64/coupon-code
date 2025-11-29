// src/routes/_authenticated.tsx
import { createFileRoute, Outlet, redirect } from '@tanstack/react-router'
import MobileLayout from '../layouts/AppLayout'
import { useAuthStore } from '../stores/useAuthStore'
import { api } from '../lib/api-client'
import { useEffect } from 'react'

export const Route = createFileRoute('/_authenticated')({
    beforeLoad: () => {
        if (!useAuthStore.getState().isAuthenticated) {
            throw redirect({ to: '/login' })
        }
    },




    component: () => {
        const { user, logout } = useAuthStore()

        useEffect(function () {

            console.log('tae')
           const v =  api.get('http://localhost:8000/api/debug-user').then(function (response) {

                console.log(response);

            });
        });

        return (
            <MobileLayout>
                {/* Inject real wallet balance & logout */}
                <AuthenticatedProvider user={user!} logout={logout} />
                <Outlet />
            </MobileLayout>
        )
    },
})

// Tiny provider so your layout can read auth without importing store everywhere
function AuthenticatedProvider({
    user,
    logout
}: {
    user: any
    logout: () => Promise<void>
}) {
    // This patches your existing layout to use real data
    // We'll override CreditIndicator and UserDropdown below
    return null
}
