<!-- Navigation -->
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-shield-alt text-3xl text-indigo-600 mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">ConsentRX</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-indigo-600 transition">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="{{ route('pages.terms') }}" class="text-gray-600 hover:text-indigo-600 transition">
                    Terms
                </a>
                <a href="{{ route('pages.privacy') }}" class="text-gray-600 hover:text-indigo-600 transition">
                    Privacy
                </a>
                <a href="{{ route('pages.refund') }}" class="text-gray-600 hover:text-indigo-600 transition">
                    Refund
                </a>
                <div class="relative group">
                    <button class="flex items-center text-gray-600 hover:text-indigo-600 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('admin.login') }}" class="block px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <i class="fas fa-user-shield mr-2"></i>Admin Login
                        </a>
                        <a href="{{ route('org.login', ['organization' => 1]) }}" class="block px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition">
                            <i class="fas fa-building mr-2"></i>Organization Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
