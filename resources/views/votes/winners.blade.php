@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6 max-w-4xl">
        <h2 class="text-3xl font-bold text-center text-green-700 mb-8">🎉 الفائزون بالتصويت</h2>

        {{-- الفائز بالرئاسة --}}
        <div class="bg-white shadow rounded-lg p-6 mb-10 text-center">
            <h3 class="text-2xl font-bold text-blue-800 mb-4">🏆 الفائز بالرئاسة</h3>
            @if($presidentWinner)
                <img src="{{ $presidentWinner->photo }}" alt="صورة" class="w-28 h-28 object-cover rounded-full mx-auto mb-4 ">
                <h4 class="text-xl font-semibold">{{ $presidentWinner->name }}</h4>
                <p class="text-gray-600 mt-2 text-right">{{ $presidentWinner->bio }}</p>
            @else
                <p class="text-gray-500">لم يتم التصويت بعد.</p>
            @endif
        </div>

        {{-- الفائزون كأعضاء --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-2xl font-bold text-green-700 mb-6 text-right">👥 الأعضاء الفائزون</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-right">
                @forelse($memberWinners as $member)
                    <div class="border rounded-lg p-4 text-center">
                        <img src="{{ $member->photo }}" alt="صورة" class="w-20 h-20 object-cover rounded-full block ml-auto mb-3">


                        <h4 class="text-lg font-bold text-right">{{ $member->name }}</h4>
                        <p class="text-gray-600 text-right">{{ $member->bio }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-2">لا توجد نتائج بعد.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
