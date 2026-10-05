import React from 'react';
import { createRoot } from 'react-dom/client';
import { Agentation } from 'agentation';

// Only mount Agentation visual feedback toolbar in local environment / localhost
if (
    typeof window !== 'undefined' &&
    (window.location.hostname === 'localhost' ||
     window.location.hostname === '127.0.0.1' ||
     window.location.hostname.endsWith('.test') ||
     window.location.hostname.endsWith('.local'))
) {
    document.addEventListener('DOMContentLoaded', () => {
        let container = document.getElementById('agentation-root');
        if (!container) {
            container = document.createElement('div');
            container.id = 'agentation-root';
            document.body.appendChild(container);
        }
        const root = createRoot(container);
        root.render(
            <Agentation 
                appName="MarketLink (eGreen Basket)"
                endpoint="http://localhost:4747"
            />
        );
    });
}
