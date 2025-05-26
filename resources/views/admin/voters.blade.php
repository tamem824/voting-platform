@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-4xl p-6">
        <h2 class="text-3xl font-bold mb-6 text-center text-blue-700">قائمة المصوتين</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow">
                {{ session('success') }}
            </div>
        @endif

        @if($voters->isEmpty())
            <p class="text-center text-gray-500 text-lg">لا يوجد مصوتين حتى الآن.</p>
        @else
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 table-auto text-right">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-200">#</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-200">الاسم</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-200">رقم العضوية</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-200">رقم الهاتف</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-200">وقت التسجيل</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 border border-gray-200">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($voters as $voter)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="px-4 py-3 text-sm border border-gray-200">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm border border-gray-200">{{ $voter->name }}</td>
                            <td class="px-4 py-3 text-sm border border-gray-200">{{ $voter->membership_number }}</td>
                            <td class="px-4 py-3 text-sm border border-gray-200">{{ $voter->phone }}</td>
                            <td class="px-4 py-3 text-sm border border-gray-200">{{ $voter->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm border border-gray-200 text-center">
                                <a href="{{ route('admin.voters.show', $voter->id) }}"
                                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                     التفاصيل
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        @endif
    </div>
@endsection
