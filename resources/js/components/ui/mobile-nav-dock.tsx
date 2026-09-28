"use client";
import React, { useState, useEffect } from "react";
import { cn } from "@/lib/utils";
import {
  AnimatePresence,
  motion,
} from "motion/react";
import {
  IconHome,
  IconMapPin,
  IconPackage,
  IconUsers,
  IconInfoCircle,
  IconMail,
} from "@tabler/icons-react";

export type DockItem = {
  title: string;
  icon: React.ReactNode;
  href: string;
  subItems?: { title: string; href: string }[];
};

export const defaultDockItems: DockItem[] = [
  {
    title: "Home",
    icon: <IconHome className="h-full w-full" />,
    href: "/",
  },
  {
    title: "Markets",
    icon: <IconMapPin className="h-full w-full" />,
    href: "/markets",
    subItems: [
      { title: "All Markets & Map", href: "/markets" },
      { title: "Nearby Markets", href: "/markets/nearby" },
    ],
  },
  {
    title: "Farm Products",
    icon: <IconPackage className="h-full w-full" />,
    href: "/products",
  },
  {
    title: "Farmers",
    icon: <IconUsers className="h-full w-full" />,
    href: "/farmers",
  },
  {
    title: "About Us",
    icon: <IconInfoCircle className="h-full w-full" />,
    href: "/about",
  },
  {
    title: "Contact",
    icon: <IconMail className="h-full w-full" />,
    href: "/contact",
  },
];

export const MobileNavDock = ({
  items = defaultDockItems,
  className,
}: {
  items?: DockItem[];
  className?: string;
}) => {
  const [expandedItem, setExpandedItem] = useState<string | null>(null);
  const [showHints, setShowHints] = useState(true);

  useEffect(() => {
    const timer = setTimeout(() => {
      setShowHints(false);
    }, 2500);
    return () => clearTimeout(timer);
  }, []);

  return (
    // block md:hidden = MOBILE ONLY. Do not remove this split.
    // Desktop nav is a separate, untouched component — this dock
    // must never render at md breakpoint and up.
    <div
      className={cn(
        "fixed right-4 top-1/2 z-50 flex -translate-y-1/2 flex-col gap-3 md:hidden",
        className,
      )}
      style={{
        right: "calc(1rem + env(safe-area-inset-right, 0px))",
      }}
    >
      {items.map((item) => (
        <div key={item.title} className="relative group">
          <AnimatePresence>
            {expandedItem === item.title && item.subItems && (
              <motion.div
                initial={{ opacity: 0, x: 10, scale: 0.95 }}
                animate={{ opacity: 1, x: 0, scale: 1 }}
                exit={{ opacity: 0, x: 10, scale: 0.95 }}
                transition={{ duration: 0.15 }}
                className="absolute right-full top-1/2 -translate-y-1/2 mr-3 z-50 flex flex-col gap-1 rounded-xl bg-white/95 dark:bg-neutral-900/95 backdrop-blur-md p-1.5 shadow-2xl border border-neutral-200 dark:border-neutral-800 min-w-[170px]"
              >
                {item.subItems.map((sub) => (
                  <a
                    key={sub.title}
                    href={sub.href}
                    className="flex items-center justify-between whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold text-neutral-800 hover:bg-emerald-50 hover:text-emerald-700 dark:text-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                  >
                    <span>{sub.title}</span>
                    <IconChevronRight className="h-3.5 w-3.5 text-neutral-400" />
                  </a>
                ))}
              </motion.div>
            )}
          </AnimatePresence>

          <button
            type="button"
            aria-label={item.title}
            onClick={() =>
              item.subItems
                ? setExpandedItem(expandedItem === item.title ? null : item.title)
                : (window.location.href = item.href)
            }
            className="group flex h-11 w-11 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-black/5 dark:bg-neutral-900 active:scale-95 transition-transform"
          >
            <div className="h-5 w-5 text-neutral-700 dark:text-neutral-200">
              {item.icon}
            </div>
          </button>

          {/* Label shown on first render / long-press — fixes "no label"
              issue found in critique. Fades after 2.5s per item on mount. */}
          <span
            className={cn(
              "pointer-events-none absolute right-full top-1/2 mr-2 -translate-y-1/2 whitespace-nowrap rounded-md bg-black/80 px-2 py-1 text-xs text-white transition-opacity duration-300 group-hover:opacity-100 group-active:opacity-100",
              showHints && expandedItem !== item.title ? "opacity-100" : "opacity-0"
            )}
          >
            {item.title}
          </span>
        </div>
      ))}
    </div>
  );
};

export default MobileNavDock;
