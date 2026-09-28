import React from 'react';
import { createRoot } from 'react-dom/client';
import { Testimonials } from './components/ui/testimonials-columns-1';

document.addEventListener('DOMContentLoaded', () => {
    const mountPoint = document.getElementById('home-testimonials-root');
    if (mountPoint) {
        const root = createRoot(mountPoint);
        root.render(
            <React.StrictMode>
                <Testimonials />
            </React.StrictMode>
        );
    }
});
