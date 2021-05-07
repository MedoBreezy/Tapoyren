<div class="featured_categories">

    <h2>@tr('popular_topics')</h2>

    <div class="category_list">

        @foreach(App\Category::where('parent_id','!=',null)->inRandomOrder()->take(6)->get() as $cat)
        <div class="category">
            <h3 style="margin-bottom: 20px;">{{ $cat->__('title') }}</h3>
            <!-- <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Veritatis architecto quos porro ab
               repellat</p> -->
            <a href="{{ url('category/'.$cat->id) }}" class="transitioned hover-opacity">KATEQORİYAYA GET</a>
        </div>
        @endforeach

    </div>

    <div>
        <a href="#">
            <br />
        </a>
    </div>

</div>