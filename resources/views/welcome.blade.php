<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ConsentRX - Digital Consent Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.navigation')

    <!-- Hero Section -->
    <section class="bg-white text-gray-800 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-center md:text-left">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">Consent</h1>
                    <p class="text-xl md:text-2xl mb-4 opacity-90">Get Verified Consent — Fast, Secure & Digital.</p>
                    <p class="text-lg md:text-xl mb-8 opacity-80">Revolutionizing consent management with paperless, KYC-verified, video-enabled, and court-admissible records for any organization.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <button class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-rocket mr-2"></i>Get Started
                        </button>
                        <button class="border-2 border-indigo-600 text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-600 hover:text-white transition">
                            <i class="fas fa-play-circle mr-2"></i>Watch Demo
                        </button>
                    </div>
                </div>
                <div class="text-center">
                    <img src="{{ asset('images/consent-hero.png') }}" alt="Digital Consent Process" class="rounded-lg shadow-2xl max-w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">About Consent</h2>
                    <p class="text-lg text-gray-600 mb-4">
                        Consent is a digital consent management system built for hospitals, clinics, diagnostic centers, telemedicine platforms, gyms, yoga centers, wellness centers, and any organization where consent is required. It replaces traditional paper-based consent with a secure, efficient, and eco-friendly process.
                    </p>
                    <p class="text-lg text-gray-600 mb-4">
                        Users can give informed consent instantly via QR code and OTP — no app required.
                    </p>
                    <p class="text-lg text-gray-600 mb-4">
                        Developed by <strong>Aryaneel Infotex</strong>, a leading provider of medical and healthcare support services, Consent is a dedicated digital solution created to streamline and modernize the consent process.
                    </p>
                    <p class="text-lg text-gray-600 mb-4">
                        As a product of Aryaneel Infotex, Consent reflects the company's ongoing commitment to innovation, legal compliance, and patient-centric healthcare delivery.
                    </p>
                    <p class="text-lg text-gray-600">
                        Our system ensures full transparency, secure storage, and time-stamped digital records for every consent, making it ideal for procedures, diagnostics, or clinical trials. Consent enhances trust, reduces paperwork, and improves operational efficiency for healthcare providers.
                    </p>
                </div>
                <div class="text-center">
                    <div class="bg-indigo-100 rounded-lg p-8">
                        <i class="fas fa-hospital text-6xl text-indigo-600 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Healthcare Focused</h3>
                        <p class="text-gray-600">Built specifically for medical and healthcare environments</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Consent Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Why Choose Consent?</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-qrcode text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Instant QR Consent</h3>
                    <p class="text-gray-600">Instant consent via QR code</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Secure OTP Verification</h3>
                    <p class="text-gray-600">Secure OTP-based user verification</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Digital Audit Trail</h3>
                    <p class="text-gray-600">Auto-generated digital audit trail</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hospital text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Healthcare Platforms</h3>
                    <p class="text-gray-600">Tailored for hospitals, clinics, diagnostics labs, telemedicine platforms, gyms, yoga centers, wellness centers, and any organization requiring consent</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">No App Needed</h3>
                    <p class="text-gray-600">No app install needed — works on mobile browsers</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Location Tracking</h3>
                    <p class="text-gray-600">User Geolocation & IP Logging</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-pdf text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">PDF Reports</h3>
                    <p class="text-gray-600">Consent reports available instantly in PDF format</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cloud text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Cloud Storage</h3>
                    <p class="text-gray-600">Encrypted Cloud Storage</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wallet text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Payment Integration</h3>
                    <p class="text-gray-600">Payment Wallets Integration for Consent Fees</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-video text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Multi-Language</h3>
                    <p class="text-gray-600">Video & PDF Consent in Regional Language</p>
                </div>
                <div class="text-center card-hover p-6 rounded-lg">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Admin Dashboard</h3>
                    <p class="text-gray-600">Hospital Admin Dashboard</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white text-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12">
                <!-- Company Info -->
                <div>
                    <h3 class="text-lg font-bold mb-3">About Consent</h3>
                    <p class="text-sm text-gray-600 mb-4">Consent is committed to transforming user consent with secure, fast, and user-friendly digital solutions.</p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-3">Quick Links</h3>
                    <ul class="space-y-1">
                        <li><a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition">Home</a></li>
                        <li><a href="#about" class="text-sm text-gray-600 hover:text-indigo-600 transition">About</a></li>
                        <li><a href="#features" class="text-sm text-gray-600 hover:text-indigo-600 transition">Services</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600 transition">Pricing Details</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition">Terms & Policies</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-bold mb-3">Contact Us</h3>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            KK Market, D wing, Office No. 88, Dhankawadi, Pune- 411043, Maharashtra, India
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-phone mr-2"></i>
                            +91 8600006546
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2"></i>
                            aryaneelinfotex@gmail.com
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-globe mr-2"></i>
                            lifeshieldinfoteq.in
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-300 mt-6 pt-6 text-center">
                <p class="text-sm text-gray-600 font-bold">Copyright © 2025 Consent - All Rights Reserved</p>
                <p class="text-sm text-gray-600 mt-1 font-bold">Powered By Aryaneel Infotex software Services</p>
                <div class="mt-3 space-x-6">
                    <a href="{{ route('pages.privacy') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition font-bold">Privacy Policy</a>
                    <a href="{{ route('pages.refund') }}" class="text-sm text-gray-600 hover:text-indigo-600 transition font-bold">Refund Policy</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
