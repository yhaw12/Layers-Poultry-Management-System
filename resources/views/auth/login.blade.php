@extends('layouts.app')

@section('content')
<style>
    /* Premium Spring Animations */
    @keyframes smoothZoom {
        0% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    @keyframes slideUpSpring {
        0% { opacity: 0; transform: translateY(40px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideRightSpring {
        0% { opacity: 0; transform: translateX(-40px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes buttonShine {
        0% { left: -100%; }
        20% { left: 100%; }
        100% { left: 100%; }
    }

    /* Animation Classes */
    .animate-bg-zoom {
        animation: smoothZoom 10s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    .animate-slide-up {
        opacity: 0;
        animation: slideUpSpring 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-slide-right {
        opacity: 0;
        animation: slideRightSpring 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    
    /* Staggered Delays */
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
    .delay-400 { animation-delay: 400ms; }
    .delay-500 { animation-delay: 500ms; }

    /* Button Shine Effect */
    .btn-shine::after {
        content: '';
        position: absolute;
        top: 0;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
        transform: skewX(-25deg);
        animation: buttonShine 6s infinite;
    }
</style>

<div class="min-h-screen w-full flex bg-slate-50 dark:bg-slate-900 transition-colors duration-500 ease-in-out relative overflow-hidden">
    
    <div class="absolute inset-0 w-full lg:w-1/2 h-full z-0 overflow-hidden bg-slate-900">
        <img src="{{ asset('images/chicken.jpg') }}" alt="Farm Background" class="absolute inset-0 w-full h-full object-cover opacity-70 animate-bg-zoom origin-center">
        
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md lg:backdrop-blur-none lg:bg-gradient-to-t lg:from-slate-900/95 lg:via-slate-900/40 lg:to-transparent transition-all duration-500"></div>
        
        <div class="hidden lg:flex relative z-10 flex-col justify-end p-16 w-full h-full">
            <div class="mb-8 animate-slide-right delay-200">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-white rounded-2xl shadow-lg mb-6 transform hover:scale-110 hover:rotate-3 transition-all duration-300 ease-out">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-4xl font-extrabold text-white tracking-tight mb-4 leading-tight">Poultry Tracker</h2>
                <p class="text-slate-300 text-lg font-medium max-w-md leading-relaxed opacity-90">Smart flock management and real-time financial analytics for the modern farm.</p>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 relative z-10 flex flex-col items-center justify-center p-4 sm:p-8 lg:p-16 lg:bg-white lg:dark:bg-slate-900 transition-colors duration-500 ml-auto min-h-screen">
        
        <div class="w-full max-w-md bg-white/95 dark:bg-slate-900/95 lg:bg-transparent lg:dark:bg-transparent backdrop-blur-xl lg:backdrop-blur-none shadow-2xl shadow-black/20 lg:shadow-none rounded-[2rem] lg:rounded-none p-8 sm:p-10 lg:p-0 border border-white/60 dark:border-slate-700/50 lg:border-none transition-all duration-500">
            
            <div class="lg:hidden flex flex-col items-center mb-8 animate-slide-up delay-100">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl shadow-lg shadow-blue-600/30 mb-4 ring-4 ring-blue-600/10">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Poultry Tracker</h2>
            </div>

            <div class="mb-8 lg:mb-10 text-center lg:text-left animate-slide-up delay-200">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">Welcome back</h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 font-medium">Please enter your details to sign in.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5 lg:space-y-6">
                @csrf
                
                <div class="space-y-1.5 lg:space-y-2 text-left animate-slide-up delay-300">
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="block w-full px-5 py-3.5 sm:py-4 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 dark:text-white transition-all duration-300 ease-out shadow-sm focus:shadow-md @error('email') border-red-500 focus:ring-red-500 @enderror"
                           placeholder="admin@farm.com">
                    @error('email')
                        <p class="text-red-500 text-sm font-medium mt-1 ml-1 animate-slide-up">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5 lg:space-y-2 text-left animate-slide-up delay-400">
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Password</label>
                    <input id="password" type="password" name="password" required 
                           class="block w-full px-5 py-3.5 sm:py-4 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 dark:text-white transition-all duration-300 ease-out shadow-sm focus:shadow-md @error('password') border-red-500 focus:ring-red-500 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="text-red-500 text-sm font-medium mt-1 ml-1 animate-slide-up">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1 pb-2 px-1 animate-slide-up delay-500">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="remember" id="remember" class="peer appearance-none w-5 h-5 border-2 border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800 checked:bg-blue-600 checked:border-blue-600 focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 dark:focus:ring-offset-slate-900 transition-all duration-200 ease-out cursor-pointer active:scale-90">
                            <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity duration-200 ease-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors duration-200">Remember me</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">Forgot password?</a>
                </div>

                <div class="animate-slide-up delay-500">
                    <button type="submit" class="relative overflow-hidden w-full bg-blue-600 text-white font-bold text-base py-4 rounded-xl shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300 ease-out btn-shine">
                        <span class="relative z-10">Sign In</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/60 text-center animate-slide-up delay-500">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-bold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 ml-1">Request access</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection