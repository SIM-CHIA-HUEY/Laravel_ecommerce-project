@include('head')
    <!-- TOP SCREEN : FIXED -->
    <div class="sticky-top">
        <!-- HEADER : Logo, buttons of postAds/search/login or logout-->
        @include('header')
        <!-- SEARCHBAR : Looking for... in which location... -->
        @include('searchbar')
        <!-- CATEGORIES : Cars & vehicles, For sale, etc. -->
        @include('categorybar')
    </div>
    <!-- RESULTS -->
    <div class="container px-md-4 bg-lightblue">
        @isset($categoryList)
        <div class="d-flex flex-row align-items-center">
            <div><a href='/' class='btn' style="font-size:smaller;"><i class="bi bi-house-door text-duckblue"></i> Home </a></div>
            <div><i class="bi bi-chevron-right"></i></div>
            @foreach($categoryList as $category)
                <div><a href="/category/{{ $category->id }}" style="font-size:smaller;" class="btn">{{ $category->name}}</a></div>
                <div style="font-size:smaller;" ><i class="bi bi-chevron-right"></i></div>
            @endforeach
        </div>
        @endisset
        <div class="row p-md-3">
            @include('filters')
            @include('adcard')
        </div>
        @include('navlink')
    </div>
<!-- FOOTER -->
@include('foot')