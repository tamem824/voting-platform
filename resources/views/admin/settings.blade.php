@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-xl p-6">
        <h2 class="text-2xl font-bold mb-4 text-center">إعدادات وقت التصويت</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label for="starting_vote" class="block mb-1 font-semibold">بداية التصويت</label>
                <input type="datetime-local" name="starting_vote" id="starting_vote" class="w-full border p-2 rounded"
                       value="{{ old('starting_vote', \Carbon\Carbon::parse($setting->starting_vote)->format('Y-m-d\TH:i')) }}">
            </div>

            <div>
                <label for="ending_vote" class="block mb-1 font-semibold">نهاية التصويت</label>
                <input type="datetime-local" name="ending_vote" id="ending_vote" class="w-full border p-2 rounded"
                       value="{{ old('ending_vote', \Carbon\Carbon::parse($setting->ending_vote)->format('Y-m-d\TH:i')) }}">
            </div>

            <div>
                <label for="is_active" class="block mb-1 font-semibold">هل التصويت مفعل؟</label>
                <select name="is_active" id="is_active" class="w-full border p-2 rounded">
                    <option value="1" {{ $setting->is_active ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ !$setting->is_active ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded w-full">
                حفظ الإعدادات
            </button>
        </form>
    </div>
@endsection
