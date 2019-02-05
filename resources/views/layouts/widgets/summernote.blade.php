<input type="hidden" id="mention-route-entities" value="{{ route('search.mentions') }}"/>
<input type="hidden" id="mention-route-months" value="{{ route('search.months') }}"/>

@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js" defer></script>
    <script src="{{ asset('js/summernote.js') }}" defer></script>
@endsection

@section('styles')
    <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
@endsection