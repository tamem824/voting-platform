@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 max-w-5xl" dir="rtl">

        {{-- زر عرض الفائزين --}}
        @if(!$settings)
            <a href="{{ route('votes.winners') }}"
               class="block w-full sm:w-auto text-center bg-green-600 hover:bg-green-700 text-white text-lg font-bold py-3 px-6 rounded-xl shadow-md transition duration-200 mx-auto mt-8">
                👑 عرض الفائزين
            </a>
        @endif

        {{-- التصويت متاح --}}
        @if($settings)

            {{-- لم يصوت بعد --}}
            @if(!$is_voted)
                <h2 class="text-3xl font-extrabold text-center text-green-700 mb-6">🗳️ نموذج التصويت</h2>

                {{-- رسائل النجاح والخطأ --}}
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

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-900 p-4 rounded-lg mb-4 shadow-sm">
                        <ul class="list-disc list-inside space-y-1 text-right">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <style>
                    .selected-card {
                        background-color: #d1fae5 !important;
                        border-color: #10b981 !important;
                        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.5) !important;
                    }
                    #member-warning {
                        display: none;
                        position: fixed;
                        top: 20%;
                        left: 50%;
                        transform: translateX(-50%);
                        background-color: #baa8a8;
                        color: #b91c1c;
                        border: 1px solid #f87171;
                        padding: 12px 20px;
                        border-radius: 8px;
                        font-size: 0.875rem;
                        box-shadow: 0 4px 8px rgba(150, 28, 28, 0.3);
                        z-index: 9999;
                        text-align: center;
                        min-width: 250px;
                    }
                    .candidate-img {
                        transition: transform 0.3s ease;
                    }
                    label:hover .candidate-img {
                        transform: scale(1.05);
                    }
                </style>

                {{-- نافذة التأكيد --}}
                <div id="confirmModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden items-center justify-center z-50 flex">
                    <div class="bg-white p-6 rounded-xl max-w-md w-full shadow-xl text-right">
                        <h2 class="text-lg font-bold mb-4 text-gray-800">هل أنت متأكد من اختياراتك؟</h2>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p><strong>الرئيس:</strong> <span id="selectedPresident"></span></p>
                            <p><strong>الأعضاء:</strong></p>
                            <ul class="list-disc list-inside space-y-1" id="selectedMembers"></ul>
                        </div>
                        <div class="flex justify-end gap-4 mt-6">
                            <button onclick="hideModal()" class="bg-green-200 hover:bg-green-300 text-green-800 py-2 px-4 rounded-lg">تراجع</button>
                            <button onclick="submitVote()" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">تأكيد التصويت</button>
                        </div>
                    </div>
                </div>

                {{-- نموذج التصويت --}}
                <form method="POST" action="{{ route('votes.store') }}" class="bg-white p-6 sm:p-8 rounded-xl shadow-lg space-y-8">
                    @csrf

                    {{-- اختيار الرئيس --}}
                    <fieldset>
                        <legend class="text-xl font-semibold mb-4 text-right rounded px-4 py-2 bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-6 h-6 mr-2 text-green-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c2.21 0 4-1.79 4-4S14.21 3 12 3 8 4.79 8 7s1.79 4 4 4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21v-4a5 5 0 0110 0v4"/>
                            </svg>

                            اختر رئيس واحد
                        </legend>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                            @foreach($presidentCandidates as $candidate)
                                <label class="block bg-white rounded-xl shadow-md p-4 cursor-pointer hover:shadow-xl border border-gray-200 text-center hover:scale-105 transition-transform">
                                    <input type="radio" name="president_id" value="{{ $candidate->id }}" required class="hidden">
                                    <img src="{{ $candidate->photo }}" alt="{{ $candidate->name }}" class="candidate-img w-24 h-24 object-cover rounded-full border mb-3 mx-auto">
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $candidate->name }}</h3>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="my-10 border-t border-green-300"></div>

                    {{-- اختيار الأعضاء --}}
                    <fieldset class="mt-8">
                        <legend class="text-xl font-semibold mb-4 text-right rounded px-4 py-2 bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer transition flex items-center justify-end gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-6 h-6 text-green-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            اختر 4 أعضاء
                        </legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($memberCandidates as $candidate)
                                <label class="block bg-white rounded-xl shadow-md p-4 cursor-pointer hover:shadow-xl border border-gray-200 text-center hover:scale-105 transition-transform">
                                    <input type="checkbox" name="member_ids[]" value="{{ $candidate->id }}" class="hidden">
                                    <img src="{{ $candidate->photo }}" alt="{{ $candidate->name }}" class="candidate-img w-20 h-20 object-cover rounded-full border mb-3 mx-auto">
                                    <h3 class="text-md font-bold text-gray-800 mb-2">{{ $candidate->name }}</h3>
                                </label>
                            @endforeach
                        </div>
                        <p id="member-warning" class="text-red-600 mt-2 text-sm hidden text-right">يمكنك اختيار 4 أعضاء فقط.</p>
                    </fieldset>

                    {{-- زر الإرسال --}}
                    <button type="button" onclick="confirmChoices()" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-semibold transition block mx-auto flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        إرسال التصويت
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

        <hr class="my-12">
        <h2 class="text-2xl font-bold text-center text-green-700 mb-6">📊 نسبة التصويت لكل مرشح</h2>


        <div id="loading-msg" class="text-center text-gray-500 mb-6">جاري تحميل النتائج...</div>
        <div id="live-results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"></div>

    </div>


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
                                <div class="relative w-full bg-gray-200 h-6 rounded-full mb-3 overflow-hidden">
  <div class="bg-green-500 h-6 rounded-full transition-all duration-300 flex items-center justify-center text-white font-bold" style="width: ${candidate.percentage}%">
    ${candidate.percentage} %
  </div>
</div>

                                <a href="${detailUrl}" class="mt-auto inline-block bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded transition duration-200">
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

        const warning = document.getElementById('member-warning');
        const checkboxes = document.querySelectorAll('input[name="member_ids[]"]');

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const checked = document.querySelectorAll('input[name="member_ids[]"]:checked').length;
                if (checked > 4) {
                    this.checked = false;
                    warning.style.display = 'block';
                    setTimeout(() => {
                        warning.style.display = 'none';
                    }, 3000);
                }
            });
        });

        const cards = document.querySelectorAll('label.block');

        cards.forEach(card => {
            const input = card.querySelector('input[type="radio"], input[type="checkbox"]');
            if (!input) return;

            if (input.checked) {
                card.classList.add('selected-card');
            }

            input.addEventListener('change', () => {
                if (input.type === 'radio') {
                    const name = input.name;
                    document.querySelectorAll(`input[name="${name}"]`).forEach(i => {
                        i.closest('label').classList.remove('selected-card');
                    });
                    if (input.checked) {
                        card.classList.add('selected-card');
                    }
                } else if (input.type === 'checkbox') {
                    if (input.checked) {
                        card.classList.add('selected-card');
                    } else {
                        card.classList.remove('selected-card');
                    }
                }
            });
        });
        function confirmChoices() {
            const selectedPresidentInput = document.querySelector('input[name="president_id"]:checked');
            const selectedMemberInputs = document.querySelectorAll('input[name="member_ids[]"]:checked');

            // التحقق من اختيار رئيس
            if (!selectedPresidentInput) {
                alert("يرجى اختيار رئيس واحد.");
                return;
            }

            // التحقق من اختيار 4 أعضاء بالضبط
            if (selectedMemberInputs.length !== 4) {
                alert("يرجى اختيار 4 أعضاء بالضبط.");
                return;
            }

            // عرض البيانات في المودال
            const selectedPresidentName = selectedPresidentInput.closest('label').querySelector('h3')?.innerText || "غير محدد";

            const selectedMemberNames = Array.from(selectedMemberInputs).map(input => {
                return input.closest('label').querySelector('h3')?.innerText || '';
            });

            document.getElementById('selectedPresident').innerText = selectedPresidentName;

            const membersList = document.getElementById('selectedMembers');
            membersList.innerHTML = '';
            selectedMemberNames.forEach(name => {
                const li = document.createElement('li');
                li.textContent = name;
                membersList.appendChild(li);
            });

            // عرض المودال
            document.getElementById('confirmModal').classList.remove('hidden');
        }


        function hideModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        function submitVote() {
            hideModal();
            document.querySelector('form').submit();
        }

    </script>
@endsection
