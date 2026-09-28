import React from "react";
import { MobileNavDock } from "@/components/ui/mobile-nav-dock";
import {
  IconHome,
  IconMapPin,
  IconPackage,
  IconUsers,
  IconInfoCircle,
  IconMail,
} from "@tabler/icons-react";

export default function MobileNavDockDemo() {
  const items = [
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

  return <MobileNavDock items={items} />;
}
