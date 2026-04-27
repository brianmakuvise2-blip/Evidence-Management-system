@extends('layouts.admin')

@section('title', 'About - Evidence Management System')
@section('page-title', 'About')

@section('content')
    <!-- Hero Section with brand value -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="badge bg-light text-dark mb-3 py-2 px-3 rounded-pill"><i class="bi bi-database-check"></i> Zimbabwe Justice Ecosystem</div>
                    <h1 class="hero-title">Centralized Evidence Management <br> For a Transparent Future</h1>
                    <p class="hero-sub mt-3">A secure, immutable platform uniting law enforcement, judiciary, and oversight bodies — ensuring integrity, chain of custody, and operational excellence.</p>
                    {{-- <div class="mt-4 d-flex flex-wrap gap-3 justify-content-center">
                        <a href="#" class="btn btn-light px-4 py-2 rounded-pill fw-semibold"><i class="bi bi-person-plus"></i> Request Demo</a>
                        <a href="#" class="btn btn-outline-light px-4 py-2 rounded-pill"><i class="bi bi-file-earmark-text"></i> Documentation</a>
                    </div> --}}
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
                    <p class="text-secondary">Live tracking of evidence item #ZIM-8923-24 — "Financial Transaction Records"</p>
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

       

        </div>
    </div>
@endsection

@push('styles')
<style>
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
@endpush