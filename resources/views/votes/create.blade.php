@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-4xl p-6">
        <h2 class="text-2xl font-bold mb-4 text-center">نموذج التصويت</h2>

        {{-- الرسائل --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
        @endif

        {{-- الأخطاء --}}
        @if ($errors->any())
            <div class="bg-red-50 text-red-800 p-4 rounded mb-4">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- نموذج التصويت --}}
        <form method="POST" action="{{ route('votes.store') }}" class="space-y-4 bg-white p-6 rounded shadow">
            @csrf

            <h3 class="text-lg font-semibold">اختر رئيس واحد</h3>
            @foreach($presidentCandidates as $candidate)
                <label class="block">
                    <input type="radio" name="president_id" value="{{ $candidate->id }}" required>
                    {{ $candidate->name }}
                </label>
            @endforeach

            <h3 class="text-lg font-semibold mt-6">اختر 4 أعضاء</h3>
            @foreach($memberCandidates as $candidate)
                <label class="block">
                    <input type="checkbox" name="member_ids[]" value="{{ $candidate->id }}">
                    {{ $candidate->name }}
                </label>
            @endforeach

            <p class="text-sm text-gray-600" id="member-warning" style="display: none;">يمكنك اختيار 4 أعضاء فقط.</p>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded mt-4 w-full">
                إرسال التصويت
            </button>
        </form>

        {{-- نتائج التصويت --}}
        <hr class="my-8">
        <h2 class="text-2xl font-bold mb-4 text-center">نسبة التصويت لكل مرشح</h2>

        <div id="loading-msg" class="text-center text-gray-500">جاري تحميل النتائج...</div>
        <div id="live-results" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
    </div>

    {{-- JavaScript --}}
    <script>
        const resultsContainer = document.getElementById('live-results');
        const loadingMsg = document.getElementById('loading-msg');

        function fetchResults() {
            fetch("{{ route('votes.results') }}")
                .then(response => response.json())
                .then(data => {
                    loadingMsg.style.display = 'none';
                    resultsContainer.innerHTML = '';

                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<p class="text-center col-span-3 text-gray-600">لا توجد نتائج بعد.</p>';
                        return;
                    }

                    data.forEach(candidate => {
                        resultsContainer.innerHTML += `
                        <div class="bg-white p-4 rounded shadow text-center">
                            ${candidate.photo
                            ? `<img src="${candidate.photo}" class="w-24 h-24 object-cover rounded-full mx-auto mb-2">`
                            : `<div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">لا صورة</div>`}
                            <h3 class="font-semibold">${candidate.name}</h3>
                            <p class="text-sm text-gray-600 mb-1">نوع المرشح: ${candidate.type === 'president' ? 'رئيس' : 'عضو'}</p>
                            <p class="text-sm mb-1">نسبة التصويت: ${candidate.percentage}%</p>
                            <div class="bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full" style="width: ${candidate.percentage}%;"></div>
                            </div>
                        </div>
                    `;
                    });
                })
                .catch(error => {
                    loadingMsg.textContent = 'فشل تحميل النتائج، يرجى المحاولة لاحقًا.';
                });
        }

        fetchResults();
        setInterval(fetchResults, 5000);

        // منع اختيار أكثر من 4 أعضاء
        const checkboxes = document.querySelectorAll('input[name="member_ids[]"]');
        const warning = document.getElementById('member-warning');

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const checked = document.querySelectorAll('input[name="member_ids[]"]:checked').length;
                if (checked > 4) {
                    this.checked = false;
                    warning.style.display = 'block';
                    setTimeout(() => warning.style.display = 'none', 3000);
                }
            });
        });
    </script>
@endsection
