<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms & Conditions - ConsentRX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('partials.navigation')

    <!-- Header -->
    <section class="bg-white text-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <i class="fas fa-file-contract mr-4"></i>Terms & Conditions
            </h1>
            <p class="text-xl opacity-90">Please read these terms and conditions carefully before using our service</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="prose max-w-none">
                    <h2 class="text-2xl font-bold mb-4">1. Acceptance of Terms</h2>
                    <p class="text-gray-600">Welcome to Aryaneel Infotex Software Services. By accessing or using our website and services, you agree to comply with and be bound by the following terms:</p>
                    
                    <h2 class="text-2xl font-bold mb-4">2. Intellectual Property</h2>
                    <p class="text-gray-600">All materials and tools provided on this website are the intellectual property of Aryaneel Infotex Software Services.</p>
                    
                    <h2 class="text-2xl font-bold mb-4 mt-6">3. Lawful Use</h2>
                    <p class="text-gray-600">Our services must be used only for lawful purposes. Any unauthorized use may result in suspension or termination.</p>
                    
                    <h2 class="text-2xl font-bold mb-4 mt-6">4. Account Security</h2>
                    <p class="text-gray-600">Users are responsible for keeping their login credentials confidential.</p>
                    
                    <h2 class="text-2xl font-bold mb-4 mt-6">5. Terms Modification</h2>
                    <p class="text-gray-600">We reserve the right to modify these terms or discontinue services at any time without prior notice.</p>
                    
                    <h2 class="text-2xl font-bold mb-4 mt-6">6. Acceptance of Changes</h2>
                    <p class="text-gray-600">Continued use of our services after changes implies acceptance of those changes.</p>
                    
                                        
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
