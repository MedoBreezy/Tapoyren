@extends('layouts.admin')


@section('content')
<div class="mdk-header-layout js-mdk-header-layout">

   <div class="mdk-header-layout__content page-content">

      <div style="width: 80%; margin: 0 auto; padding: 15px;">

         <ul>
            @foreach(App\Category::where('parent_id',null)->get() as $category)
            <li>
               <a href="#">{{ $category->__('title') }}</a>
               <a class="btn btn-danger btn-sm text-white" href="{{ url('admin/category/'.$category->id.'/delete') }}">Delete</a>
               <ul>
                  @foreach($category->sub_categories as $subCat)
                  <li>
                     <a href="#">{{ $subCat->__('title') }}</a>
                     <a class="btn btn-danger btn-sm text-white" href="{{ url('admin/category/'.$subCat->id.'/delete') }}">Delete</a>
                  </li>
                  @endforeach
               </ul>
            </li>
            @endforeach
         </ul>

      </div>

   </div>

</div>

@endsection
