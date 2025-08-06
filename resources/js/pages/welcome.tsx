import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="School Attendance System">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6 text-gray-900 lg:justify-center lg:p-8 dark:from-gray-900 dark:via-gray-800 dark:to-indigo-900 dark:text-gray-100">
                <header className="mb-8 w-full max-w-6xl">
                    <nav className="flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <div className="text-3xl">🏫</div>
                            <span className="text-xl font-semibold text-indigo-600 dark:text-indigo-400">SchoolTrack</span>
                        </div>
                        <div className="flex items-center gap-4">
                            {auth.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-lg hover:shadow-xl"
                                >
                                    Go to Dashboard →
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('login')}
                                        className="inline-flex items-center px-5 py-2 text-indigo-600 font-medium hover:text-indigo-800 transition-colors duration-200 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Log in
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-lg hover:shadow-xl"
                                    >
                                        Get Started
                                    </Link>
                                </>
                            )}
                        </div>
                    </nav>
                </header>

                <div className="flex w-full items-center justify-center">
                    <main className="w-full max-w-6xl">
                        {/* Hero Section */}
                        <div className="text-center mb-16">
                            <h1 className="text-5xl lg:text-6xl font-bold mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                📚 School Attendance System
                            </h1>
                            <p className="text-xl lg:text-2xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                                Streamline attendance tracking with role-based access for admins, teachers, and students. 
                                Modern, responsive, and easy to use.
                            </p>
                            <div className="flex flex-wrap justify-center gap-4 mb-8">
                                <div className="flex items-center space-x-2 bg-green-100 dark:bg-green-900/30 px-4 py-2 rounded-full">
                                    <span className="text-green-600 dark:text-green-400">✓</span>
                                    <span className="text-sm font-medium">Real-time Tracking</span>
                                </div>
                                <div className="flex items-center space-x-2 bg-blue-100 dark:bg-blue-900/30 px-4 py-2 rounded-full">
                                    <span className="text-blue-600 dark:text-blue-400">✓</span>
                                    <span className="text-sm font-medium">Multi-Role Access</span>
                                </div>
                                <div className="flex items-center space-x-2 bg-purple-100 dark:bg-purple-900/30 px-4 py-2 rounded-full">
                                    <span className="text-purple-600 dark:text-purple-400">✓</span>
                                    <span className="text-sm font-medium">Responsive Design</span>
                                </div>
                            </div>
                        </div>

                        {/* Features Grid */}
                        <div className="grid md:grid-cols-3 gap-8 mb-16">
                            {/* Admin Features */}
                            <div className="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-shadow duration-300">
                                <div className="text-4xl mb-4">👨‍💼</div>
                                <h3 className="text-xl font-bold mb-4 text-indigo-600 dark:text-indigo-400">Admin Dashboard</h3>
                                <ul className="space-y-3 text-gray-600 dark:text-gray-300">
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Comprehensive attendance statistics and analytics</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Full management of students, teachers & classes</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Generate detailed attendance reports</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Review and approve leave requests</span>
                                    </li>
                                </ul>
                            </div>

                            {/* Teacher Features */}
                            <div className="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-shadow duration-300">
                                <div className="text-4xl mb-4">👩‍🏫</div>
                                <h3 className="text-xl font-bold mb-4 text-green-600 dark:text-green-400">Teacher Portal</h3>
                                <ul className="space-y-3 text-gray-600 dark:text-gray-300">
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>View daily teaching schedule at a glance</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Quick attendance recording for classes</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Track student attendance history</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Manage leave request approvals</span>
                                    </li>
                                </ul>
                            </div>

                            {/* Student Features */}
                            <div className="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 hover:shadow-2xl transition-shadow duration-300">
                                <div className="text-4xl mb-4">👨‍🎓</div>
                                <h3 className="text-xl font-bold mb-4 text-purple-600 dark:text-purple-400">Student Access</h3>
                                <ul className="space-y-3 text-gray-600 dark:text-gray-300">
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>View personal attendance records</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Check daily class schedule</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Submit leave requests with evidence</span>
                                    </li>
                                    <li className="flex items-start space-x-2">
                                        <span className="text-green-500 mt-1">•</span>
                                        <span>Track attendance percentage by subject</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {/* Key Features Section */}
                        <div className="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 lg:p-12 text-white mb-16">
                            <div className="text-center mb-8">
                                <h2 className="text-3xl font-bold mb-4">✨ Why Choose SchoolTrack?</h2>
                                <p className="text-xl opacity-90">Built for modern schools with cutting-edge technology</p>
                            </div>
                            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div className="text-center">
                                    <div className="text-3xl mb-3">📱</div>
                                    <h4 className="font-semibold mb-2">Responsive Design</h4>
                                    <p className="text-sm opacity-80">Works perfectly on all devices</p>
                                </div>
                                <div className="text-center">
                                    <div className="text-3xl mb-3">⚡</div>
                                    <h4 className="font-semibold mb-2">Lightning Fast</h4>
                                    <p className="text-sm opacity-80">Optimized for speed and performance</p>
                                </div>
                                <div className="text-center">
                                    <div className="text-3xl mb-3">🔐</div>
                                    <h4 className="font-semibold mb-2">Secure Access</h4>
                                    <p className="text-sm opacity-80">Role-based permissions and security</p>
                                </div>
                                <div className="text-center">
                                    <div className="text-3xl mb-3">📊</div>
                                    <h4 className="font-semibold mb-2">Rich Analytics</h4>
                                    <p className="text-sm opacity-80">Detailed reports and insights</p>
                                </div>
                            </div>
                        </div>

                        {/* CTA Section */}
                        {!auth.user && (
                            <div className="text-center bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl">
                                <h2 className="text-2xl font-bold mb-4">Ready to Get Started? 🚀</h2>
                                <p className="text-gray-600 dark:text-gray-300 mb-6">
                                    Join thousands of schools already using SchoolTrack for efficient attendance management.
                                </p>
                                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                    <Link
                                        href={route('register')}
                                        className="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                    >
                                        Create Account
                                    </Link>
                                    <Link
                                        href={route('login')}
                                        className="inline-flex items-center justify-center px-8 py-3 border-2 border-indigo-600 text-indigo-600 font-semibold rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200"
                                    >
                                        Sign In
                                    </Link>
                                </div>
                            </div>
                        )}
                    </main>
                </div>

                <footer className="mt-16 text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>Built with ❤️ using Laravel & React • Secure • Fast • Reliable</p>
                </footer>
            </div>
        </>
    );
}