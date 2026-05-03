<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — EvidenceEMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #1e3a5f;
            --primary-light: #2b4c7c;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-900: #0f172a;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        header .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.1rem;
        }
        header .brand { font-size: 1.1rem; font-weight: 700; color: var(--primary); line-height: 1.1; }
        header .brand small { display: block; font-size: 0.7rem; font-weight: 400; color: var(--gray-500); }
        .page { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .card {
            background: white; border-radius: 1rem;
            border: 1px solid var(--gray-200);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 3rem 2.5rem; max-width: 500px; width: 100%; text-align: center;
        }
        .icon-wrap {
            width: 80px; height: 80px; border-radius: 50%;
            background: #dbeafe; border: 2px solid #93c5fd;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.75rem; font-size: 2.25rem; color: #1d4ed8;
        }
        .error-code { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-400); margin-bottom: 0.5rem; }
        h1 { font-size: 1.6rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem; }
        p { color: var(--gray-500); font-size: 0.9375rem; line-height: 1.65; margin-bottom: 1.75rem; }
        .actions { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.625rem 1.25rem; border-radius: 0.5rem;
            font-size: 0.9rem; font-weight: 500; text-decoration: none;
            border: none; cursor: pointer; transition: all 0.15s ease;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-light); color: white; }
        .btn-outline { background: white; color: var(--gray-700); border: 1px solid var(--gray-200); }
        .btn-outline:hover { background: var(--gray-50); color: var(--primary); }
    </style>
</head>
<body>
    <header>
        <div class="logo-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="brand">EvidenceEMS<small>Republic of Zimbabwe</small></div>
    </header>
    <div class="page">
        <div class="card">
            <div class="icon-wrap"><i class="bi bi-search"></i></div>
            <div class="error-code">Error 404 &mdash; Page Not Found</div>
            <h1>We couldn't find that page</h1>
            <p>The page you're looking for may have been moved, deleted, or the link may be incorrect. Double-check the address and try again.</p>
            <div class="actions">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-outline">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-house"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
