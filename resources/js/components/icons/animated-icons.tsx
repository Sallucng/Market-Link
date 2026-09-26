import React from "react";
import { motion } from "motion/react";

interface IconProps {
  className?: string;
  size?: number;
}

/**
 * 21st.dev Community-style Animated Home Icon
 * Features roof spring + door bounce on hover
 */
export const AnimatedHomeIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.18, y: -2 }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 400, damping: 17 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <motion.path
        d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
        whileHover={{ strokeWidth: 2.3 }}
      />
      <motion.polyline
        points="9 22 9 12 15 12 15 22"
        whileHover={{ y: [-1, 0] }}
        transition={{ duration: 0.2 }}
      />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Markets / Map Pin Icon
 * Features spring bounce + beacon pulse
 */
export const AnimatedMarketIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.2, y: -3, rotate: [0, -5, 5, 0] }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 450, damping: 15 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
      <motion.circle
        cx="12"
        cy="10"
        r="3"
        whileHover={{ scale: [1, 1.4, 1] }}
        transition={{ duration: 0.5, repeat: Infinity, repeatDelay: 1 }}
      />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Products Basket Icon
 * Features wobble & pop motion
 */
export const AnimatedProductsIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.2, rotate: [-6, 6, -3, 0] }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 400, damping: 14 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <path d="m7.5 4.27 9 5.15" />
      <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
      <motion.path
        d="m3.3 7 8.7 5 8.7-5"
        whileHover={{ y: [-1, 1, 0] }}
      />
      <path d="M12 22V12" />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Farmers / Community Icon
 * Features waving hand / tilt motion
 */
export const AnimatedFarmersIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.18, y: -2 }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 400, damping: 16 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
      <motion.circle
        cx="9"
        cy="7"
        r="4"
        whileHover={{ y: [-1, -3, -1] }}
        transition={{ duration: 0.3 }}
      />
      <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
      <path d="M16 3.13a4 4 0 0 1 0 7.75" />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Cart Icon
 * Features wheel rolling + cart spring bounce
 */
export const AnimatedCartIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.2, x: [0, 2, -1, 0] }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 450, damping: 15 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <circle cx="8" cy="21" r="1" />
      <circle cx="19" cy="21" r="1" />
      <motion.path
        d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"
        whileHover={{ strokeWidth: 2.3 }}
      />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Info / About Icon
 * Features 360 degree smooth flip & glow
 */
export const AnimatedAboutIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.2, rotate: 180 }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 350, damping: 20 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <circle cx="12" cy="12" r="10" />
      <path d="M12 16v-4" />
      <path d="M12 8h.01" />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Dashboard / User Icon
 * Features upward spring lift
 */
export const AnimatedDashboardIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.18, y: -2 }}
    whileTap={{ scale: 0.92 }}
    transition={{ type: "spring", stiffness: 420, damping: 15 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <rect width="7" height="9" x="3" y="3" rx="1" />
      <rect width="7" height="5" x="14" y="3" rx="1" />
      <rect width="7" height="9" x="14" y="12" rx="1" />
      <rect width="7" height="5" x="3" y="16" rx="1" />
    </svg>
  </motion.div>
);

/**
 * 21st.dev Community-style Animated Search Icon
 * Features magnifying zoom and rotate
 */
export const AnimatedSearchIcon = ({ className = "h-5 w-5" }: IconProps) => (
  <motion.div
    whileHover={{ scale: 1.25, rotate: 15 }}
    whileTap={{ scale: 0.9 }}
    transition={{ type: "spring", stiffness: 450, damping: 14 }}
    className={`inline-flex items-center justify-center ${className}`}
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="w-full h-full"
    >
      <circle cx="11" cy="11" r="8" />
      <path d="m21 21-4.3-4.3" />
    </svg>
  </motion.div>
);
