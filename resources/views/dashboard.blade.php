@extends('layouts.app')

@section('title', 'Dashboard · Task Management API')

@section('content')
<div class="flex min-h-full items-center justify-center px-4">
    <div class="text-center">
        <p class="text-sm text-slate-500">Signed in as</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ auth()->user()->name }}</p>
        <p class="mt-6 text-sm text-slate-400">The task dashboard is under construction.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Sign out
            </button>
        </form>
    </div>
</div>
@endsection
