<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Zimbabwe Evidence Management System | Secure & Centralized</title>
    <!-- Bootstrap 5 CSS + Icons + Google Fonts for professional typography -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN for crisp icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter for clean modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7fc;
            color: #1e2a3e;
            line-height: 1.5;
        }

        /* refined navbar with better elevation */
        .navbar {
            background: linear-gradient(135deg, #0a1c2f 0%, #0f2b3f 100%);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            padding: 0.9rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: -0.3px;
            background: linear-gradient(120deg, #fff, #c9e9ff);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent !important;
        }

        .btn-outline-light-custom {
            border: 1px solid rgba(255,255,255,0.4);
            background: transparent;
            color: white;
            border-radius: 40px;
            padding: 0.45rem 1.4rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-outline-light-custom:hover {
            background-color: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.8);
            transform: translateY(-1px);
        }

        /* Hero section with gradient overlay */
        .hero-section {
            background: linear-gradient(109.6deg, rgb(12, 35, 56) 11.2%, rgb(27, 56, 78) 91.1%);
            color: white;
            border-radius: 0 0 2rem 2rem;
            margin-bottom: 3rem;
            padding: 3.5rem 1rem 4rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(66,153,225,0.2) 0%, rgba(66,153,225,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.8rem;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }

        .hero-sub {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* stats cards / metrics row */
        .stat-card {
            background: white;
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 12px 28px -8px rgba(0, 32, 64, 0.12);
            transition: transform 0.2s, box-shadow 0.2s;
            padding: 1.25rem 0.5rem;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 32px -12px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.4rem;
            color: #1e6f5c;
            background: #e0f2f0;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 60px;
            margin: 0 auto 1rem auto;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #0f2b3f;
            line-height: 1.2;
        }

        /* feature cards modern */
        .feature-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.8rem 1.5rem;
            transition: all 0.25s ease-in-out;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 8px 20px rgba(0,0,0,0.02);
            height: 100%;
        }

        .feature-card:hover {
            border-color: #d4e2ed;
            box-shadow: 0 20px 30px -12px rgba(0, 32, 64, 0.12);
        }

        .feature-icon {
            font-size: 2.5rem;
            background: #eef2fa;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1.2rem;
            margin-bottom: 1.5rem;
            color: #1a4b6e;
        }

        .section-title {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.75rem;
            color: #0b2b3b;
            position: relative;
            display: inline-block;
        }

        .section-divider {
            width: 70px;
            height: 4px;
            background: #1e6f5c;
            margin: 0.5rem auto 2rem auto;
            border-radius: 4px;
        }

        /* custody timeline */
        .chain-badge {
            background: #eef2fa;
            padding: 0.4rem 1rem;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e4663;
        }

        .audit-log-item {
            border-left: 3px solid #1e6f5c;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }

        /* CTA section */
        .cta-section {
            background: linear-gradient(110deg, #eef5f9 0%, #ffffff 100%);
            border-radius: 2rem;
            padding: 2.5rem;
            margin-top: 2rem;
            border: 1px solid #e2edf2;
        }

        .btn-primary-custom {
            background: #0f2b3f;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: 40px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-primary-custom:hover {
            background: #1a4b6e;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15,43,63,0.2);
        }

        .btn-outline-accent {
            border: 1.5px solid #1e6f5c;
            color: #1e6f5c;
            background: white;
            border-radius: 40px;
            padding: 0.7rem 2rem;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-outline-accent:hover {
            background: #1e6f5c;
            color: white;
        }

        footer {
            background: #0f1e2c;
            color: #bdd3e6;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .stat-number {
                font-size: 1.6rem;
            }
            .section-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>

<!-- Professional Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-shield-shaded me-2"></i>Evidence<span style="font-weight:400">EMS</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center gap-2">
                <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Cases</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Audit Trail</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Institutions</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="/login" class="btn btn-outline-light-custom"><i class="bi bi-box-arrow-in-right me-1"></i>Secure Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section with brand value -->
<section class="hero-section">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="badge bg-light text-dark mb-3 py-2 px-3 rounded-pill"><i class="bi bi-database-check"></i> Zimbabwe Justice Ecosystem</div>
                <h1 class="hero-title">Centralized Evidence Management <br> For a Transparent Future</h1>
                <p class="hero-sub mt-3">A secure, immutable platform uniting law enforcement, judiciary, and oversight bodies — ensuring integrity, chain of custody, and operational excellence.</p>
                <div class="mt-4 d-flex flex-wrap gap-3 justify-content-center">
                    <a href="#" class="btn btn-light px-4 py-2 rounded-pill fw-semibold"><i class="bi bi-person-plus"></i> Request Demo</a>
                    <a href="#" class="btn btn-outline-light px-4 py-2 rounded-pill"><i class="bi bi-file-earmark-text"></i> Documentation</a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mt-4">
    <!-- Metrics / key stats row (dynamic professional look) -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-files"></i></div>
                <div class="stat-number">2,847</div>
                <div class="text-secondary">Active Evidence Items</div>
                <small class="text-success"><i class="bi bi-arrow-up-short"></i> +12.4%</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-building"></i></div>
                <div class="stat-number">6</div>
                <div class="text-secondary">Institutions Connected</div>
                <small>Police · Courts · ACC · NPA</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <div class="stat-number">100%</div>
                <div class="text-secondary">Chain-of-Custody Integrity</div>
                <small>Blockchain-anchored logs</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-number">98.6%</div>
                <div class="text-secondary">Audit Compliance Rate</div>
                <small>Real-time tracking</small>
            </div>
        </div>
    </div>

    <!-- Central Features: Secure, Centralized, Chain of Custody (expanded) -->
    <div class="row g-4 mb-5">
        <div class="col-12 text-center">
            <h2 class="section-title">Built on Trust & Transparency</h2>
            <div class="section-divider"></div>
            <p class="text-secondary col-md-8 mx-auto">Our platform delivers end‑to‑end encryption, role‑based access, and complete evidence lifecycle oversight.</p>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-lock-fill"></i></div>
                <h4>Military‑Grade Security</h4>
                <p class="text-secondary">AES-256 encryption at rest and TLS 1.3 in transit. Granular permissions ensure only authorized personnel access sensitive evidence.</p>
                <div class="mt-2"><span class="chain-badge"><i class="bi bi-check-circle-fill text-success me-1"></i> FIPS 140-2 compliant</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-diagram-3"></i></div>
                <h4>Unified Central Platform</h4>
                <p class="text-secondary">Connect Zimbabwe Anti-Corruption Commission, Zimbabwe Republic Police, Judiciary Service Commission, and prosecutors in one seamless ecosystem.</p>
                <div class="mt-2"><span class="chain-badge"><i class="bi bi-share"></i> Real-time interoperability</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon"><i class="bi bi-link-45deg"></i></div>
                <h4>Immutable Chain of Custody</h4>
                <p class="text-secondary">Every transfer, access, or review is timestamped and logged. Digital signatures and geolocation metadata preserve legal admissibility.</p>
                <div class="mt-2"><span class="chain-badge"><i class="bi bi-qr-code"></i> NFC/QR handover logs</span></div>
            </div>
        </div>
    </div>

    <!-- Chain of Custody & Audit Trail Demo Section (professional mockup) -->
    <div class="row g-5 align-items-stretch mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-journal-bookmark-fill fs-2" style="color:#1e6f5c;"></i>
                    <h3 class="h4 mb-0 fw-bold">Chain of Custody Timeline</h3>
                </div>
                <p class="text-secondary">Live tracking of evidence item #ZIM-8923-24 — “Financial Transaction Records”</p>
                <div class="audit-log-item">
                    <small class="text-muted"><i class="bi bi-calendar-event"></i> 12 Oct 2024, 09:14</small>
                    <div class="fw-semibold">Collected by Det. M. Ncube (ZRP CID)</div>
                    <div>Location: Harare Central Police Station · Sealed container #E2241</div>
                </div>
                <div class="audit-log-item">
                    <small class="text-muted"><i class="bi bi-arrow-repeat"></i> 15 Oct 2024, 11:30</small>
                    <div class="fw-semibold">Transferred to Forensic Lab (Government Analyst)</div>
                    <div>Chain Custody Officer: S. Zhou · Digital signature verified</div>
                </div>
                <div class="audit-log-item">
                    <small class="text-muted"><i class="bi bi-person-badge"></i> 22 Oct 2024, 14:20</small>
                    <div class="fw-semibold">Viewed by Prosecutor L. Dube (NPA)</div>
                    <div>Read-only access · Tamper-proof audit trail</div>
                </div>
                <div class="mt-2 text-end"><a href="#" class="text-decoration-none">View full history <i class="bi bi-chevron-right"></i></a></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-fingerprint fs-2" style="color:#1e6f5c;"></i>
                    <h3 class="h4 mb-0 fw-bold">Audit & Compliance Dashboard</h3>
                </div>
                <p class="text-secondary">Real-time oversight dashboard — every access recorded, non-repudiable logs.</p>
                <div class="mt-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Evidence Integrity Score</span>
                        <span class="fw-bold">99.7%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 99.7%"></div>
                    </div>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> 2,431 evidence items with verified chain logs</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> 0 integrity breaches (last 12 months)</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Full court-admissible audit reports (PDF/CSV)</li>
                        <li><i class="bi bi-shield-plus me-2"></i> Multi-factor authentication enforced</li>
                    </ul>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill mt-2">Generate compliance report</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Institutional integration / ecosystem map -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="section-title">United Justice Ecosystem</h2>
            <div class="section-divider"></div>
            <p class="text-secondary">Connecting key Zimbabwean institutions for seamless evidence collaboration</p>
        </div>
        <div class="col-md-12">
            <div class="row g-3 text-center mt-2">
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                        <i class="bi bi-bank fs-1" style="color:#1e6f5c;"></i>
                        <h6 class="mt-2">Zimbabwe Republic Police</h6>
                        <small>Investigation & seizure</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                        <i class="bi bi-gavel fs-1" style="color:#1e6f5c;"></i>
                        <h6 class="mt-2">Judiciary of Zimbabwe</h6>
                        <small>Court evidence management</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                        <i class="bi bi-file-earmark-spreadsheet fs-1" style="color:#1e6f5c;"></i>
                        <h6 class="mt-2">National Prosecuting Authority</h6>
                        <small>Case preparation & review</small>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                        <i class="bi bi-person-badge fs-1" style="color:#1e6f5c;"></i>
                        <h6 class="mt-2">ZACC & Anti-Corruption</h6>
                        <small>Forensic evidence tracking</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA for system access and security assurance -->
    {{-- <div class="cta-section mb-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h3 class="fw-bold">Empower justice with verifiable evidence integrity</h3>
                <p class="mb-0 mt-2">Join Zimbabwe's leading digital transformation in evidence governance. Role-based dashboards, court-ready reporting, and 24/7 monitoring.</p>
                <div class="mt-3 d-flex flex-wrap gap-3">
                    <a href="/login" class="btn btn-primary-custom text-white"><i class="bi bi-key"></i> Access secure portal</a>
                    <a href="#" class="btn btn-outline-accent"><i class="bi bi-calendar-week"></i> Schedule advisory</a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <i class="bi bi-shield-lock-fill" style="font-size: 4rem; color: #1e6f5c;"></i>
                <p class="mt-2 small">Audited by independent cybersecurity partners</p>
            </div>
        </div>
    </div>
</div> --}}

{{-- <!-- Footer with professional links and copyright -->
<footer class="pt-5 pb-4 mt-4">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5 class="text-white mb-3"><i class="bi bi-shield-shaded"></i> Evidence Management System</h5>
                <p class="small">A centralized, secure digital platform serving Zimbabwe's justice and governance ecosystem. Chain-of-custody, transparency, and operational resilience.</p>
            </div>
            <div class="col-md-2 col-6">
                <h6 class="text-white-50">Platform</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none small">Features</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Security</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Compliance</a></li>
                </ul>
            </div>
            <div class="col-md-2 col-6">
                <h6 class="text-white-50">Institutions</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none small">ZRP</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Judiciary</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">NPA</a></li>
                </ul>
            </div>
            {{-- <div class="col-md-4">
                <h6 class="text-white-50">Support & Legal</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none small">helpdesk@evidence.gov.zw</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">+263 242 123 456</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Privacy & Data Protection</a></li>
                </ul>
            </div> --}}
        {{-- </div>
        <hr class="mt-4 mb-3 bg-secondary">
        <div class="row">
            <div class="col-md-6 text-center text-md-start small">
                © 2026 Evidence Management System — Zimbabwe. All rights reserved. Version 2.4
            </div>
            <div class="col-md-6 text-center text-md-end small">
                <i class="bi bi-database-check"></i> Chain of Custody Certified | ISO 27001 Framework
            </div>
        </div> --}}
    {{-- </div>
</footer> --}} --}}

<!-- Bootstrap JS Bundle for interactive components -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>