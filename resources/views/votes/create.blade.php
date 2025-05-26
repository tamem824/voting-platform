@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 max-w-5xl" dir="rtl">

        @if(!$settings)
            <a href="{{ route('votes.winners') }}"
               class="block w-full sm:w-auto text-center bg-green-600 hover:bg-green-700 text-white text-lg font-bold py-3 px-6 rounded-xl shadow-md transition duration-200 mx-auto mt-8">
                👑 عرض الفائزين
            </a>
        @endif

        {{-- حالة التصويت --}}
        @if($settings)
            @if(!$is_voted)
                <h2 class="text-3xl font-extrabold text-center text-blue-700 mb-6">🗳️ نموذج التصويت</h2>

                {{-- الرسائل --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded-lg mb-4 shadow-md text-center">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-300 text-red-800 p-4 rounded-lg mb-4 shadow-md text-center">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- الأخطاء --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-900 p-4 rounded-lg mb-4 shadow-sm">
                        <ul class="list-disc list-inside space-y-1 text-right">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- نموذج التصويت --}}
                <form method="POST" action="{{ route('votes.store') }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-lg space-y-8">
                    @csrf

                    {{-- اختيار الرئيس --}}
                    <fieldset>
                        <legend class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">👤 اختر رئيس واحد</legend>
                        <div class="space-y-3">
                            @foreach($presidentCandidates as $candidate)
                                <label class="flex items-center justify-end text-gray-800 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded">
                                    <span class="ml-3">{{ $candidate->name }}</span>
                                    <input type="radio" name="president_id" value="{{ $candidate->id }}" required class="text-blue-600 focus:ring-blue-500">
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- اختيار الأعضاء --}}
                    <fieldset>
                        <legend class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">👥 اختر 4 أعضاء</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($memberCandidates as $candidate)
                                <label class="flex items-center justify-end text-gray-800 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded">
                                    <span class="ml-3">{{ $candidate->name }}</span>
                                    <input type="checkbox" name="member_ids[]" value="{{ $candidate->id }}" class="text-green-600 focus:ring-green-500">
                                </label>
                            @endforeach
                        </div>
                        <p id="member-warning" class="text-red-600 mt-2 text-sm hidden text-right">يمكنك اختيار 4 أعضاء فقط.</p>
                    </fieldset>

                    {{-- زر الإرسال --}}
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                        ✅ إرسال التصويت
                    </button>
                </form>
            @else
                <div class="bg-green-50 border border-green-200 text-green-700 text-center p-6 rounded-xl shadow-md mt-8">
                    لقد قمت بالتصويت بالفعل. شكراً لمشاركتك!
                </div>
            @endif
        @else
            <div class="text-center text-gray-500 bg-yellow-50 border border-yellow-200 p-6 rounded-xl shadow-sm">
                التصويت غير متاح حالياً.
            </div>
        @endif

        {{-- نتائج التصويت --}}
        <hr class="my-12">
        <h2 class="text-2xl font-bold text-center text-indigo-700 mb-6">📊 نسبة التصويت لكل مرشح</h2>

        <div id="loading-msg" class="text-center text-gray-500 mb-6">جاري تحميل النتائج...</div>
        <div id="live-results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"></div>
    </div>

    {{-- جافا سكريبت --}}
    <script>
        const resultsContainer = document.getElementById('live-results');
        const loadingMsg = document.getElementById('loading-msg');
        const candidateDetailUrlTemplate = "{{ route('candidates.show', ':id') }}";

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
                            <div class="bg-white rounded-xl shadow-lg p-6 text-center flex flex-col items-center hover:shadow-2xl transition">
                                ${candidate.photo
                            ? `<img src="${candidate.photo}" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-full mb-4 border shadow-md">`
                            : `<div class="w-24 h-24 sm:w-28 sm:h-28 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 mb-4">لا صورة</div>`}
                                <h3 class="text-lg font-bold text-gray-800 mb-1">${candidate.name}</h3>
                                <p class="text-sm text-gray-500 mb-1">النوع: <span class="font-semibold">${candidate.type === 'president' ? 'رئيس' : 'عضو'}</span></p>
                                <p class="text-sm text-gray-600 mb-3">نسبة التصويت: <span class="text-green-600 font-semibold">${candidate.percentage}%</span></p>
                                <div class="w-full bg-gray-100 h-4 rounded-full mb-3">
                                    <div class="bg-green-500 h-4 rounded-full" style="width: ${candidate.percentage}%"></div>
                                </div>
                                <a href="${detailUrl}" class="mt-auto inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded transition duration-200">
                                    عرض التفاصيل
                                </a>
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
                    warning.classList.remove('hidden');
                    setTimeout(() => warning.classList.add('hidden'), 3000);
                }
            });
        });
    </script>
@endsection
