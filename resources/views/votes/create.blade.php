@extends('layouts.app')

@section('content')
    @if($settings)
        @if(! $is_voted)
            <div class="container mx-auto p-4 sm:p-6 max-w-full sm:max-w-2xl lg:max-w-4xl">
                <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">نموذج التصويت</h2>

                {{-- الرسائل --}}
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4 shadow">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4 shadow">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- الأخطاء --}}
                @if ($errors->any())
                    <div class="bg-red-50 text-red-900 p-4 rounded mb-4 border border-red-300 shadow-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- نموذج التصويت --}}
                <form method="POST" action="{{ route('votes.store') }}" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
                    @csrf

                    <fieldset>
                        <legend class="text-lg font-semibold mb-3">اختر رئيس واحد</legend>
                        <div class="space-y-3">
                            @foreach($presidentCandidates as $candidate)
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="president_id" value="{{ $candidate->id }}" required class="mr-3 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $candidate->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-lg font-semibold mb-3 mt-6">اختر 4 أعضاء</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($memberCandidates as $candidate)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="member_ids[]" value="{{ $candidate->id }}" class="mr-3 text-green-600 focus:ring-green-500">
                                    <span>{{ $candidate->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p id="member-warning" class="text-red-600 mt-2 text-sm hidden">يمكنك اختيار 4 أعضاء فقط.</p>
                    </fieldset>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md font-semibold transition duration-300">
                        إرسال التصويت
                    </button>
                </form>
            </div>
        @else
            <div class="container mx-auto p-4 text-center text-green-700 font-semibold">
                لقد قمت بالتصويت بالفعل. شكراً لمشاركتك!
            </div>
        @endif
    @else
        <div class="container mx-auto p-4 text-center text-gray-600">
            التصويت غير متاح حالياً.
        </div>
    @endif

    {{-- نتائج التصويت --}}
    <hr class="my-10 border-gray-300">

    <div class="container mx-auto px-4 sm:px-6 max-w-full sm:max-w-4xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">نسبة التصويت لكل مرشح</h2>

        <div id="loading-msg" class="text-center text-gray-500 mb-6">جاري تحميل النتائج...</div>
        <div id="live-results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"></div>
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
                        const detailUrl = candidateDetailUrlTemplate.replace(':id', candidate.id);

                        resultsContainer.innerHTML += `
        <div class="bg-white p-6 rounded-lg shadow-md text-center flex flex-col items-center">
            ${candidate.photo
                            ? `<img src="${candidate.photo}" alt="${candidate.name}" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-full mb-4">`
                            : `<div class="w-24 h-24 sm:w-28 sm:h-28 bg-gray-300 rounded-full mb-4 flex items-center justify-center text-gray-500">لا صورة</div>`}
            <h3 class="font-semibold text-lg mb-1">${candidate.name}</h3>
            <p class="text-sm text-gray-600 mb-2">نوع المرشح: ${candidate.type === 'president' ? 'رئيس' : 'عضو'}</p>
            <p class="text-sm mb-2">نسبة التصويت: <span class="font-semibold">${candidate.percentage}%</span></p>
            <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                <div class="bg-green-500 h-4 rounded-full" style="width: ${candidate.percentage}%;"></div>
            </div>

            <div>
                <a href="${detailUrl}"
                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-medium transition">
                    عرض التفاصيل
                </a>
            </div>
        </div>
    `;
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
                    warning.classList.remove('hidden');
                    setTimeout(() => warning.classList.add('hidden'), 3000);
                }
            });
        });
    </script>
@endsection
