@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-4xl p-6">
        <h2 class="text-2xl font-bold mb-4 text-center">قائمة المصوتين</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if($voters->isEmpty())
            <p class="text-center text-gray-600">لا يوجد مصوتين حتى الآن.</p>
        @else
            <table class="w-full table-auto border border-gray-200 rounded bg-white shadow">
                <thead>
                <tr class="bg-gray-100 text-right">
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">الاسم</th>
                    <th class="p-2 border">رقم العضوية</th>
                    <th class="p-2 border">رقم الهاتف</th>
                    <th class="p-2 border">وقت التسجيل</th>
                </tr>
                </thead>
                <tbody>
                @foreach($voters as $voter)
                    <tr>
                        <td class="p-2 border">{{ $loop->iteration }}</td>
                        <td class="p-2 border">{{ $voter->name }}</td>
                        <td class="p-2 border">{{ $voter->membership_number }}</td>
                        <td class="p-2 border">{{ $voter->phone }}</td>
                        <td class="p-2 border">{{ $voter->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
