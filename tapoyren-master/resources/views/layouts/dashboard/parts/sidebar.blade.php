<div class="vertical-menu">

   <div class="h-100">

      <div class="user-wid text-center py-4">
         @if(auth()->user()->avatar_url)
         <div class="user-img">
            <img src="{{ auth()->user()->avatar_url }}" alt="" class="avatar-md mx-auto rounded-circle">
         </div>
         @endif

         <div class="mt-3">
            <a href="#" class="text-dark font-weight-medium font-size-16">{{ auth()->user()->name }}</a>
         </div>
      </div>

      <div id="sidebar-menu">

         <ul class="metismenu list-unstyled" id="side-menu">
            @if(auth()->user()->type==='student')
            <li class="menu-title">Menu</li>

            <!-- <li>
               <a href="javascript: void(0);" class="waves-effect">
                  <i class="mdi mdi-airplay"></i><span class="badge badge-pill badge-info float-right">2</span>
                  <span>Dashboard</span>
               </a>
               <ul class="sub-menu" aria-expanded="false">
                  <li><a href="index.html">Dashboard 1</a></li>
                  <li><a href="index-2.html">Dashboard 2</a></li>
               </ul>
            </li> -->
            @endif

         </ul>
      </div>
      <!-- Sidebar -->
   </div>
</div>
