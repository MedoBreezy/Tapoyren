@extends('layouts.tapoyren')

@section('content')

@include('pages.parts.header')


<main class="page-wrapper">

   <div class="page-title">
      <h1>@tr('edit_profile')</h1>
   </div>

   <form action="{{ url('account/profile') }}" method="POST" enctype="multipart/form-data">
      @csrf

      @if($errors->any())
      <div class="error pb-24pt" style="font-weight: bold;">{{ $errors->first() }}</div>
      @endif

      <div class="edit_profile">

         <div class="profile_wrapper">
            <div class="left">
               <div class="avatar">
                  @if(auth()->user()->avatar_url!==null)
                  <img src="{{ auth()->user()->avatar_url }}" /><br />
                  @else
                  <img src="https://via.placeholder.com/150" /><br />
                  @endif
                  <div class="upload_button" onclick="uploadAvatar()">
                     <i class="material-icons">backup</i>
                  </div>
                  <input id="avatar" name="avatar" type="file" class="hidden">
               </div>
            </div>

            <div class="right">

               <div class="input">
                  <span class="placeholder">@tr('fullname')</span>
                  <input id="name" name="name" value="{{ auth()->user()->name }}" type="text"
                     placeholder="@tr('your_fullname')..." required>
               </div>

               <div class="input">
                  <span class="placeholder">@tr('email')</span>
                  <input id="email" name="email" value="{{ auth()->user()->email }}" type="email"
                     placeholder="@tr('your_email')..." required>
               </div>


               <select name="gender" id="gender" required>
                  <option value="">@tr('select_gender')</option>
                  <option value="male" {{ auth()->user()->gender==='male' ? 'selected' : ''}}>@tr('male')</option>
                  <option value="female" {{ auth()->user()->gender==='female' ? 'selected' : ''}}>@tr('female')
                  </option>
               </select>

                <select name="birthDate" style="margin-top: 10px">
                    <option value="">@tr('birth_date')</option>
                    @for($i=1960; $i<2021; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>

               <div class="input">
                  <span class="placeholder">* @tr('birth_date')</span>
                  <input id="birthDate" name="birthDate" value="{{ auth()->user()->birthDate }}" type="text"
                     data-toggle="datepicker" placeholder="@tr('your_birthdate')..." readonly required>
               </div>

               <div class="input">
                  <span class="placeholder">@tr('registration_number')</span>
                  <input id="registration_number" value="{{ auth()->user()->registration_number }}"
                     name="registration_number" type="registration_number"
                     placeholder="@tr('registration_number_description')">
               </div>

               <select name="employment" id="employment">
                  <option value="">@tr('select_your_employment_status')</option>
                  <option value="1" {{ auth()->user()->employment_status===1 ? 'selected' : ''}}>
                     @tr('employed_fulltime')</option>
                  <option value="2" {{ auth()->user()->employment_status===2 ? 'selected' : ''}}>
                     @tr('employed_parttime')</option>
                  <option value="3" {{ auth()->user()->employment_status===3 ? 'selected' : ''}}>@tr('student')
                  </option>
                  <option value="4" {{ auth()->user()->employment_status===4 ? 'selected' : ''}}>@tr('not_available')
                  </option>
               </select>


            </div>
         </div>

         <button>@tr('update')</button>
      </div>

   </form>

   @include('pages.parts.footer')

</main>

@endsection

@push('footer')

<script>
   function uploadAvatar() {
      document.getElementById('avatar').click();
   }
</script>

@endpush
