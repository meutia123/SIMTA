
@extends('layouts.app')

@section('title', $title . ' - SIMTA')
@section('page-label', $title)

@section('content')

<div class="bg-white rounded-lg border border-gray-200 p-6">

    <h2 class="text-xl font-bold mb-3">
        {{ $title }}
    </h2>

    <p class="text-sm text-gray-600">
        {{ $description }}
    </p>

    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-5">

        <h3 class="font-semibold text-blue-800">
            Halaman Berhasil Dibuka
        </h3>

        <p class="text-sm text-blue-700 mt-2">
            Fitur {{ $title }} akan dikembangkan pada tahap berikutnya.
        </p>

    </div>

    <a href="{{ route('dashboard') }}"
       class="inline-block mt-6 bg-blue-600 text-white px-5 py-2 rounded-lg">
        Kembali ke Dashboard
    </a>

</div>

@endsection
