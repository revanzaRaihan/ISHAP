@extends('layouts.app')
@section('content')
    <div class="max-w-2xl mx-auto py-20">
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-200">
            <h2 class="text-2xl font-bold mb-6">Update Pengetahuan Medis (AI Ingestion)</h2>
            @if(session('success'))
                <div class="p-4 mb-4 bg-emerald-100 text-emerald-700 rounded-xl">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 bg-rose-100 text-rose-700 rounded-xl">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 bg-rose-100 text-rose-700 rounded-xl">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Upload PDF Panduan Medis/ISPA</label>
                    <input type="file" name="pdf" class="w-full p-3 border border-slate-300 rounded-xl">
                    <p class="text-xs text-slate-500 mt-2">AI akan membaca PDF ini, mencari nama penyakit, gejala, dan
                        panduan rumah lalu menyimpannya ke database.</p>
                </div>
                <button type="submit"
                    class="w-full py-4 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition">
                    Proses dengan AI & Update Database
                </button>
            </form>
        </div>
    </div>
@endsection