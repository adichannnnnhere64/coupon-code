import * as React from 'react'
import { Outlet, createRootRoute } from '@tanstack/react-router'
import AppLayout from '../layouts/AppLayout'
import '../App.css'

export const Route = createRootRoute({
    component: RootComponent,
})

function RootComponent() {
    return (
        <React.Fragment>
            <AppLayout>
                <Outlet />
            </AppLayout>
        </React.Fragment>
    )
}
