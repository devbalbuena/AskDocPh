@extends('layouts.admin')
@section('title', 'Platform Reports')
@section('page-title', 'Platform Reports')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Data Exports & Reports</h2>
            <p class="text-sm text-gray-500">Download platform data in CSV or PDF formats.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Users Report Widget --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-50 text-blue-600 rounded-xl mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">User Demographics</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1">Export a list of registered users including their roles, verification status, and registration dates.</p>
            
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.export.users') }}" class="flex-1 text-center bg-gray-50 border border-gray-200 hover:bg-white hover:border-blue-300 text-gray-700 hover:text-blue-700 text-sm font-semibold py-2 rounded-xl transition-colors">
                    CSV Export
                </a>
                <a href="{{ route('admin.reports.export.users-pdf') }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 rounded-xl transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF Print
                </a>
            </div>
        </div>

        {{-- Appointments Report Widget --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-center w-12 h-12 bg-green-50 text-green-600 rounded-xl mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Appointments List</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1">Export a record of platform appointments, showing doctor, patient, date, and status.</p>
            
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.export.appointments') }}" class="flex-1 text-center bg-gray-50 border border-gray-200 hover:bg-white hover:border-green-300 text-gray-700 hover:text-green-700 text-sm font-semibold py-2 rounded-xl transition-colors">
                    CSV Export
                </a>
                <a href="{{ route('admin.reports.export.appointments-pdf') }}" class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-xl transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF Print
                </a>
            </div>
        </div>

        {{-- Crisis Reports Widget --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-center w-12 h-12 bg-red-50 text-red-600 rounded-xl mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Crisis Log</h3>
            <p class="text-sm text-gray-500 mb-6 flex-1">Export handled emergency platform events, panic button presses, and crisis report metadata.</p>
            
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.export.crisis') }}" class="flex-1 text-center bg-gray-50 border border-gray-200 hover:bg-white hover:border-red-300 text-gray-700 hover:text-red-700 text-sm font-semibold py-2 rounded-xl transition-colors">
                    CSV Export
                </a>
                <a href="{{ route('admin.reports.export.crisis-pdf') }}" class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded-xl transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF Print
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
