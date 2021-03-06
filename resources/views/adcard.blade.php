@if(!is_null(session('viewinline')))
<!-- LINE VIEW -->
    <div class="d-flex flex-column">
        @for($i = (($page-1)*8); $i < ($page * 8); $i++)
            @isset($ads[$i])
            <div class="card mb-1 p-lg-1">
                <div class="d-flex row">
                    <!-- Image box -->
                    <div class="col-5 col-sm-4 col-lg-2">
                        <!-- <img src="{{asset($ads[$i]->url)}}" class="img-fluid h-100" alt="Image missing">
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-duckblue">
                            <i class="bi bi-camera me-1"></i> <small>2</small>
                        </span> -->
                        <div id="carouselControls{{$ads[$i]->main_id}}" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @php
                                $j = 0
                                @endphp
                                @foreach($pictures as $picture)
                                    @if($picture->ads_id == $ads[$i]->main_id)
                                        <div class="carousel-item @if($j == 0) active @endif">
                                            <img src="{{asset($picture->url)}}" class="img-fluid h-100" alt="Image missing">
                                        </div>
                                        @php
                                        $j++
                                        @endphp
                                    @endif
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselControls{{$ads[$i]->main_id}}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselControls{{$ads[$i]->main_id}}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                    <!-- Text box -->
                    <div class="d-flex flex-column col-sm-7 col-5 col-lg-9">
                        <div class="fw-bold">{{ Str::limit($ads[$i]->title, $limit=50, $end = '...') }}</div>
                        <!-- TODO : Location to add -->
                        <div style="font-size:xx-small">{{$ads[$i]->number}} {{$ads[$i]->street}}, {{$ads[$i]->postcode}} {{$ads[$i]->city}} ({{$ads[$i]->country}})</div>
                        <div><small>{{ Str::limit($ads[$i]->description, $limit = 256, $end = '...') }}</small></div>
                        <div class="fw-bold">{{ number_format($ads[$i]->price, 2) }} $</div>
                    </div>
                    <!-- Like box -->
                    <div class="col-2 col-sm-1">
                        <button type="button" class="btn"><i class="text-danger bi bi-heart"></i></button>
                    </div>
                </div>
            </div>
            @endisset
        @endfor
    </div>
@else
<!-- BOX VIEW -->
    @for($i = (($page-1)*8); $i < ($page * 8); $i++)
        @isset($ads[$i])
        <div class="p-2 col-lg-3 col-md-6 cardad">
            <a href="{{url('category/'.$ads[$i]->category_id)}}" class="card p-0 btn" style="height:15rem;">
                <div class="card-head h-70 d-flex">
                    <div class="h-100 w-100"><img class="card-img-top" src="{{asset($ads[$i]->url)}}" alt="Image missing"></div>
                </div>
                <div class="card-body h-30 p-1 rounded-0 rounded-bottom">
                    <h5 class="card-title h-50" style="font-size:2vh;">{{ Str::upper(Str::limit($ads[$i]->title, $limit = 20, $end = '...')) }}</h5>
                    <p class="card-text h-50">{{ number_format($ads[$i]->price, 2) }} $</p>
                </div>
            </a>
        </div>
        @endisset
    @endfor
@endif