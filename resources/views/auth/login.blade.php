@extends('layouts.app')

@section('title', 'Sign in · Task Management API')

@section('content')
<div class="flex min-h-full items-center justify-center px-4 py-16">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"></path>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-slate-900">Task Management API</h1>
            <p class="mt-1 text-sm text-slate-500">Sign in to manage your tasks</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            Demo credentials: demo&#64;example.com / password
        </p>
    </div>
</div>
@endsection
