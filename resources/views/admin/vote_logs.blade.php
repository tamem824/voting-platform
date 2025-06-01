@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-semibold text-center text-blue-600 mb-8">سجلات التصويت</h2>

        <div class="overflow-x-auto shadow rounded-lg">
            <table class="min-w-full bg-white">
                <thead>
                <tr class="bg-blue-600 text-white text-center">
                    <th class="py-3 px-6">#</th>
                    <th class="py-3 px-6">المصوت</th>
                    <th class="py-3 px-6">عنوان IP</th>
                    <th class="py-3 px-6">المتصفح</th>
                    <th class="py-3 px-6">النظام</th>
                    <th class="py-3 px-6">User Agent</th>
                    <th class="py-3 px-6">التاريخ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr class="text-center border-b border-gray-200 hover:bg-blue-50">
                        <td class="py-3 px-6 whitespace-nowrap">{{ $log->id }}</td>
                        <td class="py-3 px-6 whitespace-nowrap">{{ $log->voter?->name ?? '---' }}</td>
                        <td class="py-3 px-6 whitespace-nowrap">{{ $log->ip_address }}</td>
                        <td class="py-3 px-6 whitespace-nowrap">{{ $log->browser }}</td>
                        <td class="py-3 px-6 whitespace-nowrap">{{ $log->platform }}</td>
                        <td class="py-3 px-6 whitespace-nowrap" title="{{ $log->user_agent }}">
                            {{ \Illuminate\Support\Str::limit($log->user_agent, 40) }}
                        </td>
                        <td class="py-3 px-6 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
