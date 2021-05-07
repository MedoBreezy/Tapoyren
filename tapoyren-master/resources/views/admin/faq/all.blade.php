@extends('layouts.admin')


@section('content')
<h1>FAQ</h1>
<a href="{{ url('admin/faq/add') }}" class="btn btn-sm btn-accent">ADD QUESTION</a>

<hr />

@foreach(App\FaqQuestion::orderBy('id','desc')->get() as $question)
<div style="display: flex; margin-top: 8px; align-items: center; background: white; padding: 10px; border-radius: 4px;">
    <h3 onclick="showDescription(this)" style="margin: 0; margin-right: 40px;">{{ $question->__('question') }}</h3>
    <a href="{{ url('admin/faq/'.$question->id.'/update') }}" class="btn btn-sm btn-primary mr-8pt">EDIT</a>
    <a href="{{ url('admin/faq/'.$question->id.'/delete') }}" class="btn btn-sm btn-accent">DELETE</a>
</div>
<div class="hidden" style="background: white; padding: 10px; border-radius: 4px;">
    {{ $question->description }}
</div>
@endforeach

@endsection

@push('footer')

<script type="text/javascript">
    function showDescription(e) {
        var parent = e.parentElement;
        parent.nextElementSibling.classList.toggle('hidden');
    }
</script>


@endpush