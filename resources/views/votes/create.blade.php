@extends('components.layout')

@section('content')
        <div class="container">

            <h2>نموذج التصويت</h2>

            {{-- رسائل النجاح والخطأ --}}
            @if(session('success'))
                <div style="color: green; margin-bottom: 15px;">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div style="color: red; margin-bottom: 15px;">{{ session('error') }}</div>
            @endif

            {{-- عرض أخطاء الفاليديشن --}}
            @if ($errors->any())
                <div style="color: red; margin-bottom: 15px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('votes.store') }}">
                @csrf

                <h3>اختر رئيس واحد</h3>
                @foreach($presidentCandidates as $candidate)
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="radio" name="president_id" value="{{ $candidate->id }}" required>
                        {{ $candidate->name }}
                    </label>
                @endforeach

                <h3>اختر 4 أعضاء</h3>
                @foreach($memberCandidates as $candidate)
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="checkbox" name="member_ids[]" value="{{ $candidate->id }}">
                        {{ $candidate->name }}
                    </label>
                @endforeach

                <button type="submit" style="margin-top: 15px;">إرسال التصويت</button>
            </form>

            <hr style="margin: 40px 0;">

            <h2>نسبة التصويت لكل عضو</h2>

            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                @foreach($allCandidates as $candidate)
                    <div
                        style="border: 1px solid #ccc; padding: 10px; width: 200px; text-align: center; border-radius: 6px;">
                        @if($candidate->photo)
                            <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}"
                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                        @else
                            <div
                                style="width: 150px; height: 150px; background: #ddd; line-height: 150px; border-radius: 50%;">
                                لا صورة
                            </div>
                        @endif

                        <h4 style="margin: 10px 0 5px;">{{ $candidate->name }}</h4>
                        <p>نوع المرشح: {{ ucfirst($candidate->type) }}</p>

                        @php
                            $totalVotes = $totalVotesCount ?? 1;
                            $candidateVotes = $candidateVotesCount[$candidate->id] ?? 0;
                            $percentage = round(($candidateVotes / $totalVotes) * 100, 2);
                        @endphp

                        <p>نسبة التصويت: {{ $percentage }}%</p>

                        <div style="background: #eee; height: 15px; border-radius: 8px; overflow: hidden;">
                            <div style="width: {{ $percentage }}%; background: #4caf50; height: 100%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- جافاسكريبت لمنع اختيار أكثر من 4 أعضاء --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const checkboxes = document.querySelectorAll('input[name="member_ids[]"]');
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function () {
                        const checkedCount = document.querySelectorAll('input[name="member_ids[]"]:checked').length;
                        if (checkedCount > 4) {
                            this.checked = false;
                            alert('يمكنك اختيار 4 أعضاء فقط.');
                        }
                    });
                });
            });
        </script>
    @endsection

