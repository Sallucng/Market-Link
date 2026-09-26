import React, { useEffect } from "react";
import ReactDOM from "react-dom/client";
import { FloatingDockDesktop, FloatingDockMobile } from "@/components/ui/floating-dock";
import {
  AnimatedHomeIcon,
  AnimatedMarketIcon,
  AnimatedProductsIcon,
  AnimatedFarmersIcon,
  AnimatedCartIcon,
  AnimatedAboutIcon,
  AnimatedDashboardIcon,
  AnimatedSearchIcon,
} from "@/components/icons/animated-icons";
import "./dock.css";

interface DockMountProps {
  cartCount?: number;
  auth?: boolean;
  dashboardUrl?: string;
  role?: string;
}

export const MarketLinkDock: React.FC<DockMountProps> = ({
  cartCount = 0,
  auth = false,
  dashboardUrl = "/login",
  role = "guest",
}) => {
  useEffect(() => {
    // Intercept clicks on #search to open Omnisearch modal
    const handleSearchClick = (e: MouseEvent) => {
      const target = (e.target as HTMLElement).closest('a[href="#search"]');
      if (target) {
        e.preventDefault();
        const searchBtn = document.querySelector<HTMLElement>('[data-bs-target="#globalSearchModal"]');
        if (searchBtn) {
          searchBtn.click();
        } else {
          window.location.href = "/products";
        }
      }
    };

    document.addEventListener("click", handleSearchClick);
    return () => document.removeEventListener("click", handleSearchClick);
  }, []);

  const items = [
    {
      title: "Home",
      icon: <AnimatedHomeIcon className="h-5 w-5 text-emerald-700" />,
      href: "/",
    },
    {
      title: "Markets & Map",
      icon: <AnimatedMarketIcon className="h-5 w-5 text-amber-600" />,
      href: "/markets",
    },
    {
      title: "Farm Products",
      icon: <AnimatedProductsIcon className="h-5 w-5 text-emerald-600" />,
      href: "/products",
    },
    {
      title: "Our Farmers",
      icon: <AnimatedFarmersIcon className="h-5 w-5 text-teal-700" />,
      href: "/farmers",
    },
    {
      title: "About Us",
      icon: <AnimatedAboutIcon className="h-5 w-5 text-blue-600" />,
      href: "/about",
    },
    {
      title: cartCount > 0 ? `Cart (${cartCount})` : "Pickup Cart",
      icon: (
        <div className="relative flex items-center justify-center">
          <AnimatedCartIcon className="h-5 w-5 text-emerald-800" />
          {cartCount > 0 && (
            <span className="absolute -top-1.5 -right-2 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-sm">
              {cartCount}
            </span>
          )}
        </div>
      ),
      href: "/cart",
    },
    {
      title: "Omnisearch",
      icon: <AnimatedSearchIcon className="h-5 w-5 text-indigo-600" />,
      href: "#search",
    },
    {
      title: auth ? (role === "admin" ? "Admin Panel" : role === "farmer" ? "Farmer Hub" : "My Account") : "Sign In",
      icon: <AnimatedDashboardIcon className="h-5 w-5 text-neutral-700" />,
      href: dashboardUrl,
    },
  ];

  return (
    <div className="marketlink-dock-wrapper" aria-label="Quick Navigation Dock">
      {/* Desktop macOS Magnification Dock (Bottom Center Horizontal) */}
      <div className="marketlink-dock-desktop-wrap d-none d-md-flex">
        <FloatingDockDesktop
          items={items}
          className="floating-dock-desktop-glass border border-emerald-900/10 px-4 pb-3 pt-2 shadow-2xl"
        />
      </div>

      {/* Mobile Streamlined Floating Dock (Bottom Center Trigger) */}
      <div className="marketlink-dock-mobile-wrap d-flex d-md-none">
        <FloatingDockMobile
          items={items}
          className="floating-dock-mobile-container"
        />
      </div>
    </div>
  );
};

// Auto-mount when DOM is ready
const mountId = "marketlink-floating-dock";
const mountEl = document.getElementById(mountId);

if (mountEl) {
  const cartCount = parseInt(mountEl.getAttribute("data-cart-count") || "0", 10);
  const auth = mountEl.getAttribute("data-auth") === "true";
  const dashboardUrl = mountEl.getAttribute("data-dashboard-url") || "/login";
  const role = mountEl.getAttribute("data-role") || "guest";

  const root = ReactDOM.createRoot(mountEl);
  root.render(
    <MarketLinkDock
      cartCount={cartCount}
      auth={auth}
      dashboardUrl={dashboardUrl}
      role={role}
    />
  );
}
export default MarketLinkDock;
