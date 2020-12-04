 @if(!is_null($questions))
     <h1 class="content__title">{{ $current_category ? $current_category->title : __('faq.search_results') }}</h1>

     @if(count($questions) < 1 && empty($current_category))
         <p>
             {{ __('faq.results_not_found') }}
         </p>
     @else
         @foreach($questions as $question)
             @include('pages.faq.parts.question', ['question' => $question])
         @endforeach
     @endif
 @elseif(!empty($current_category))
     @include('pages.faq.parts.static_page', ['page' => $current_category])
 @endif
