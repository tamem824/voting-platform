@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6 max-w-3xl bg-white rounded-lg shadow-md">
        <div class="flex justify-end mb-4">
            <a href="{{ route('votes.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition duration-200">
                العودة لصفحة النتائج →
            </a>
        </div>

        <div class="flex flex-col text-right">

            {{-- الصورة والمعلومات الأساسية --}}


            <div class="flex flex-row-reverse items-center gap-6 mb-8">
                @if($candidate->photo)
                    <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}"
                         class="w-32 h-32 rounded-full object-cover shadow-lg border-2 border-gray-200">
                @else
                    <div class="w-32 h-32 bg-gray-300 rounded-full flex items-center justify-center text-gray-500 shadow-inner">
                        لا صورة
                    </div>
                @endif

                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $candidate->name }}</h1>
                    <p class="text-gray-600 mb-1">
                        نوع المرشح:
                        <span class="font-semibold text-gray-800">{{ $candidate->type === 'president' ? 'رئيس' : 'عضو' }}</span>
                    </p>
                    <p class="text-gray-600">تاريخ الإضافة:
                        <span class="font-medium text-gray-700">{{ $candidate->created_at->format('Y-m-d') }}</span>
                    </p>
                </div>
            </div>

            {{-- عدد الأصوات والنسبة --}}
            <div class="mb-6 text-lg leading-relaxed text-gray-800 px-2">
                عدد الأصوات: <span class="font-semibold text-green-600">{{ $votesCount }}</span><br>
                نسبة التصويت: <span class="font-semibold text-green-600">% {{ $percentage }}</span>
            </div>

            {{-- نبذة عن المرشح --}}
            @if($candidate->bio)
                <div class="text-gray-700 mb-8 px-2">
                    <h2 class="text-xl font-semibold mb-2">نبذة عن المرشح</h2>
                    <p class="leading-relaxed">{{ $candidate->bio }}</p>
                </div>
            @endif



        </div>
    </div>
@endsection
