@extends('.layouts.app')

@section('content')

    <div class="container">
        @include('pages.faq.parts.sidebar', ['categories' => $categories])

        <section class="panel">
            @include('pages.faq.parts.search')

            <div class="content">
                @include('pages.faq.parts.content', ['current_category' => $current_category, 'questions' => $questions])
            </div>
        </section>
    </div>

@endsection
