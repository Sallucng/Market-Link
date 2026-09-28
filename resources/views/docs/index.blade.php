<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MarketLink REST API Reference & Specification</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌱</text></svg>">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #0d1117;
            color: #c9d1d9;
        }
        .topbar {
            background: #161b22;
            border-bottom: 1px solid #30363d;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #58a6ff;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .topbar-links a {
            color: #8b949e;
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 16px;
            transition: color 0.15s ease;
        }
        .topbar-links a:hover {
            color: #58a6ff;
        }
        .badge {
            background: rgba(35, 134, 54, 0.2);
            color: #3fb950;
            border: 1px solid rgba(63, 185, 80, 0.4);
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="/" class="topbar-brand">
            <span>🌱 MarketLink</span>
            <span class="badge">OpenAPI 3.0.3</span>
        </a>
        <div class="topbar-links">
            <a href="/">← Return to Storefront</a>
            <a href="/login">Portal Login</a>
            <a href="/docs/openapi.yaml" download="openapi.yaml">Download openapi.yaml</a>
        </div>
    </div>

    <script id="api-reference" data-url="/docs/openapi.yaml"></script>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
</body>
</html>
