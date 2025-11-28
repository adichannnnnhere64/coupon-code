// src/layouts/MobileLayout.tsx
import { Link, useMatchRoute } from '@tanstack/react-router';
import { Home, User, Settings } from 'lucide-react';

interface MobileLayoutProps {
    children: any;
}

interface NavItem {
    to: string;
    icon: typeof Home;
    label: string;
}

// interface CartState {
//     itemCount: number;
//     subtotal: number;
// }

// Configuration
const NAV_ITEMS: NavItem[] = [
    { to: '/', icon: Home, label: 'Home' },
    { to: '/profile', icon: User, label: 'Profile' },
    { to: '/setting', icon: Settings, label: 'Setting' },
] as const;

// Subcomponents
// function CartDropdown({ itemCount, subtotal }: CartState) {
//     return (
//         <div className="dropdown dropdown-end">
//             <div tabIndex={0} role="button" className="btn btn-ghost btn-circle" aria-label="Shopping cart">
//                 <div className="indicator">
//                     <ShoppingCart className="h-5 w-5" />
//                     {itemCount > 0 && (
//                         <span className="badge badge-sm indicator-item">{itemCount}</span>
//                     )}
//                 </div>
//             </div>
//             <div className="card card-compact dropdown-content z-10 mt-3 w-52 bg-base-100 shadow">
//                 <div className="card-body">
//                     <span className="text-lg font-bold">{itemCount} Items</span>
//                     <span className="text-info">Subtotal: ${subtotal}</span>
//                     <div className="card-actions">
//                         <Link to="/cart" className="btn btn-primary btn-block">
//                             View cart
//                         </Link>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     );
// }

function UserDropdown() {
    return (
        <div className="dropdown dropdown-end">
            <div tabIndex={0} role="button" className="btn btn-ghost btn-circle avatar" aria-label="User menu">
                <div className="w-10 rounded-full">
                    {/* <img alt="User avatar" src="" /> */}
                </div>
            </div>
            <ul className="menu dropdown-content rounded-box z-10 mt-3 w-52 bg-base-100 p-2 shadow">
                <li>
                    <Link to="/profile">
                        Profile <span className="badge badge-sm">New</span>
                    </Link>
                </li>
                <li>
                    <Link to="/settings">Settings</Link>
                </li>
                <li>
                    <button onClick={() => console.log('Logout')}>Logout</button>
                </li>
            </ul>
        </div>
    );
}

function TopNavbar() {
    return (
        <nav className="fixed top-0 left-0 right-0 z-40 bg-base-100 shadow-sm navbar" role="navigation">
            <div className="flex-1">
                <Link to="/" className="btn btn-ghost text-xl">
                    Swag
                </Link>
            </div>
            <div className="flex-none gap-2">
                <CreditIndicator amount={5000} />

                <UserDropdown />
            </div>
        </nav>
    );
}

function CreditIndicator({ amount }: { amount: number }) {
  return (
    <div className="badge badge-lg bg-base-200 text-base-content gap-1 px-3">
      <span className="font-semibold">$</span>
      <span>{amount}</span>
    </div>
  );
}



function DockNavItem({ to, icon: Icon, label }: NavItem) {
    const matchRoute = useMatchRoute();
    const isActive = matchRoute({ to, fuzzy: false });

    return (
        <Link to={to} className={`dock-btn ${isActive ? 'dock-active' : ''}`} aria-label={label} aria-current={isActive
            ? 'page' : undefined}>
            <Icon className="size-[1.2em]" />
            <span className="dock-label">{label}</span>
        </Link>
    );
}

function BottomDock() {
    return (
        <nav className="dock dock-lg sm:hidden bottom-0 left-1/2 -translate-x-1/2 z-50" role="navigation" aria-label="Bottom navigation">
            {NAV_ITEMS.map((item) => (
                <DockNavItem key={item.to} {...item} />
            ))}
        </nav>

    );
}

export default function MobileLayout({ children }: MobileLayoutProps) {
    // In a real app, this would come from context/state management
    // const cartState: CartState = {
    //     itemCount: 8,
    //     subtotal: 999,
    // };

    return (
        <div className="md:w-5xl md:grid-cols-4 md:mx-auto " >

        <div className="flex min-h-screen flex-col bg-base-100">
            <TopNavbar  />

            <main className="flex-1 overflow-y-auto pb-20 pt-16">
                {children}
            </main>

            <BottomDock />
        </div>
        </div>
    );
}
