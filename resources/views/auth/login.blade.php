@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">تسجيل الدخول</h2>

        @if(session('success'))
            <div class="mb-4 text-green-600 bg-green-100 border border-green-300 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 text-red-600 bg-red-100 border border-red-300 p-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="membership_number" class="block mb-1 text-sm font-medium text-gray-700">رقم العضوية</label>
                <input id="membership_number" type="text" name="membership_number" value="{{ old('membership_number') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 @error('membership_number') border-red-500 @enderror">
                @error('membership_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block mb-1 text-sm font-medium text-gray-700">رقم الهاتف</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 @error('phone') border-red-500 @enderror">
                @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(session('voter_id'))
                <div>
                    <label for="code" class="block mb-1 text-sm font-medium text-gray-700">رمز التحقق</label>
                    <input id="code" type="text" name="code" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200 @error('code') border-red-500 @enderror">
                    @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                    تأكيد الدخول
                </button>
            @else
                <button type="submit" name="send_code" value="1"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                    إرسال الرمز
                </button>
            @endif
        </form>
    </div>
@endsection
