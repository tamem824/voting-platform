@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-xl p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">تفاصيل المصوت</h2>

        <div class="space-y-4 text-right text-gray-700">
            <p><span class="font-semibold">الاسم:</span> {{ $voter->name }}</p>
            <p><span class="font-semibold">رقم العضوية:</span> {{ $voter->membership_number }}</p>
            <p><span class="font-semibold">رقم الهاتف:</span> {{ $voter->phone }}</p>
            <p><span class="font-semibold">البريد الإلكتروني:</span> {{ $voter->email ?? '---' }}</p>
            <p><span class="font-semibold">تاريخ التسجيل:</span> {{ $voter->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <hr class="my-8 border-gray-300">

        <h3 class="text-2xl font-semibold mb-4 text-center text-gray-800">المرشحون الذين صوت لهم المصوت</h3>

        @if($voter->votes->isEmpty())
            <p class="text-center text-gray-500 italic">لم يصوت هذا المستخدم لأي مرشح بعد.</p>
        @else
            <ul class="list-disc list-inside space-y-3 text-right text-gray-700">
                @foreach($voter->votes as $vote)
                    <li class="bg-gray-50 p-3 rounded shadow-sm flex justify-between items-center">
                        <span class="font-semibold text-gray-900">{{ $vote->candidate->name ?? '---' }}</span>
                        <span class="text-sm text-gray-500 italic px-3 py-1 rounded bg-gray-200">
                        {{ ucfirst($vote->candidate->type ?? '') }}
                    </span>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('admin.voters') }}"
               class="inline-block bg-gray-600 hover:bg-gray-700 transition text-white font-semibold px-6 py-2 rounded-lg shadow">
                العودة للقائمة
            </a>
        </div>
    </div>
@endsection
