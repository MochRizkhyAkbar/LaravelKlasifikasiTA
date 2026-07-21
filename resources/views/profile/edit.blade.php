@extends('layouts.admin_layout')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-blue-900">Profil Saya</h1>
        <p class="text-gray-500">Perbarui informasi profil dan kata sandi akun Anda.</p>
    </div>

    <div class="space-y-6">
        <div class="p-8 bg-white shadow-sm border-t-4 border-blue-900 rounded-xl">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="p-8 bg-white shadow-sm border-t-4 border-blue-900 rounded-xl">
            @include('profile.partials.update-password-form')
        </div>

        <div class="p-8 bg-white shadow-sm border-t-4 border-blue-900 rounded-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
