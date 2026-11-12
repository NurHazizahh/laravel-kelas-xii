@extends('templates.index')

@push('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/table-style.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/basictable.css') }}" />
@endpush

@push('scripts')

<script type="text/javascript" src="{{ asset('template/list-js/jquery.basictable.min.js') }}"></script>
 <script type="text/javascript">
    $(document).ready(function() {
      $('#table').basictable();
      $('#table-breakpoint').basictable({
        breakpoint: 768
      });
    });
  </script>
@endpush

@section('content')

<div class="container">
  <br />
  <!-- /w3l-medile-movies-grids -->
    <div class="agileits-single-top">
      <ol class="breadcrumb">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('movies') }}">Movies</a></li>
        <li class="active">{{ $film->title }}</li>
      </ol>
    </div>
    <div class="single-page-agile-info">
                 <!-- /movie-browse-agile -->
                         <div class="show-top-grids-w3lagile">
      <div class="col-sm-8 single-left">
        <div class="song">
          <div class="single-right-grids">

            <div class="col-md-4 single-right-grid-left">
              @php
                $detailPoster = $film->poster 
                  ? asset(str_starts_with($film->poster, 'storage/') ? $film->poster : 'storage/' . $film->poster)
                  : asset('template/images/m1.jpg');
              @endphp
              <a href="{{ route('movies.show', $film->id) }}"><img src="{{ $detailPoster }}" alt="{{ $film->title }}" onerror="this.src='{{ asset('template/images/m1.jpg') }}'" /></a>
            </div>
            <div class="col-md-8 single-right-grid-right">
              <div class="song-info">
                <h2>{{ $film->title }}</h2>	
                <p class="author"><a href="#" class="author">{{ $film->year }}</a></p>
                <p class="views">{{ $film->sinopsis }}</p>
              </div>
              <div id="myTabContent" class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledby="home-tab">
                  <div class="agile-news-table">
                    <table id="table-breakpoint">
                      <thead>
                        <tr>
                        <th>Nama Cast</th>
                        <th>Peran</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($perans as $peran)
                        <tr>
                          <td class="w3-list-info">{{ $peran->cast?->name ?? '-' }}</td>
                          <td class="w3-list-info">{{ $peran->actor }}</td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="2" class="text-center w3-list-info">Belum ada data cast</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"> </div>
          </div>
        </div>
        <div class="song-grid-right">
          <div class="share">
            <h5>Share this</h5>
            <div class="single-agile-shar-buttons">
            <ul>
              <li>
                <div class="fb-like" data-href="https://www.facebook.com/w3layouts" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7";
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
              </li>
              <li>
                <div class="fb-share-button" data-href="https://www.facebook.com/w3layouts" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.facebook.com%2Fw3layouts&amp;src=sdkpreparse">Share</a></div>
              </li>
              <li class="news-twitter">
                <a href="https://twitter.com/w3layouts" class="twitter-follow-button" data-show-count="false">Follow @w3layouts</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
              </li>
              <li>
                <a href="https://twitter.com/intent/tweet?screen_name=w3layouts" class="twitter-mention-button" data-show-count="false">Tweet to @w3layouts</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
              </li>
              <li>
                <!-- Place this tag where you want the +1 button to render. -->
                <div class="g-plusone" data-size="medium"></div>

                <!-- Place this tag after the last +1 button tag. -->
                <script type="text/javascript">
                  (function() {
                  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                  po.src = 'https://apis.google.com/js/platform.js';
                  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>
              </li>
            </ul>
          </div>
          </div>
        </div>
        <div class="clearfix"> </div>
        <div class="all-comments" id="komentar">
          <div class="all-comments-info">
            <a href="#">Comments</a>

            @if(session('success'))
              <div class="alert alert-success" style="margin-top: 15px; margin-bottom: 15px;">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
              </div>
            @endif

            <div class="agile-info-wthree-box">
              @auth
                <p style="margin-bottom: 15px; font-size: 14px; color: #333;">
                  <i class="fa fa-user-circle"></i> Berkomentar sebagai <strong>{{ auth()->user()->name }}</strong>
                </p>

                @if($errors->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px;">
                    <ul style="margin: 0; padding-left: 20px;">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('kritik.store', $film) }}" method="POST">
                  @csrf
                  <div style="margin-bottom: 15px;">
                    <label for="rating" style="font-weight: 600; color: #555; display: block; margin-bottom: 5px;">
                      Rating Film (1 - 5 Bintang): <span class="text-danger">*</span>
                    </label>
                    <select name="rating" id="rating" class="form-control" style="width: auto; min-width: 180px; display: inline-block;" required>
                      <option value="">-- Pilih Rating --</option>
                      @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                          {{ $i }} Bintang ({{ str_repeat('★', $i) }})
                        </option>
                      @endfor
                    </select>
                    @error('rating')
                      <span class="text-danger" style="display: block; font-size: 12px; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                  </div>

                  <div style="margin-bottom: 15px;">
                    <textarea name="comment" placeholder="Tulis komentar atau kritik Anda tentang film ini (3 - 500 karakter)..." required minlength="3" maxlength="500" style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">{{ old('comment') }}</textarea>
                    @error('comment')
                      <span class="text-danger" style="display: block; font-size: 12px; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                  </div>

                  <input type="submit" value="KIRIM KOMENTAR" style="background: #FF8D1B; border: none; color: #fff; padding: 10px 25px; font-weight: bold; cursor: pointer; text-transform: uppercase;">
                  <div class="clearfix"> </div>
                </form>
              @else
                <div class="alert alert-warning" style="margin-top: 15px; padding: 15px 20px; background-color: #fcf8e3; border: 1px solid #faebcc; border-radius: 4px;">
                  <p style="margin: 0; color: #8a6d3b; font-size: 14px;">
                    <i class="fa fa-info-circle"></i> Silakan login terlebih dahulu untuk menulis komentar.
                    <a href="#myModal" data-toggle="modal" data-target="#myModal" class="btn btn-warning btn-xs" style="margin-left: 10px; background-color: #FF8D1B; border-color: #e57d15; color: #fff; font-weight: bold; padding: 5px 12px;">
                      <i class="fa fa-sign-in"></i> Login Sekarang
                    </a>
                  </p>
                </div>
              @endauth
            </div>
          </div>
          <div class="media-grids">
            @forelse ($comments as $comment)
              <div class="media">
                <h5>
                  {{ $comment->user?->name ?? 'Pengguna dihapus' }}
                  <span style="font-size: 12px; color: #FF8D1B; margin-left: 10px;">
                    @for($i = 1; $i <= ($comment->rating ?? 0); $i++)
                      <i class="fa fa-star"></i>
                    @endfor
                    ({{ $comment->rating ?? '-' }}/5)
                  </span>
                  <span style="font-size: 11px; color: #888; font-weight: normal; margin-left: 10px;">
                    {{ $comment->created_at ? $comment->created_at->diffForHumans() : '-' }}
                  </span>
                </h5>
                <div class="media-left">
                  <a href="#">
                    <img src="{{ asset('storage/images/user.jpg') }}" title="One movies" alt=" " />
                  </a>
                </div>
                <div class="media-body">
                  <p>{{ $comment->comment }}</p>
                </div>
              </div>
            @empty
              <p style="padding: 20px 0; color: #777; font-style: italic;">
                Belum ada komentar. Jadilah yang pertama!
              </p>
            @endforelse
          </div>
        </div>
      </div>
      <div class="col-md-4 single-right">
        <h3>Similar Film By Genre</h3>
        @foreach ($filmByGenre as $similar)
          <div class="single-grid-right">
            <div class="single-right-grids">
              <div class="col-md-4 single-right-grid-left">
                @php
                  $similarPoster = $similar->poster 
                    ? asset(str_starts_with($similar->poster, 'storage/') ? $similar->poster : 'storage/' . $similar->poster)
                    : asset('template/images/m1.jpg');
                @endphp
                <a href="{{ route('movies.show', $similar->id) }}"><img src="{{ $similarPoster }}" alt="{{ $similar->title }}" onerror="this.src='{{ asset('template/images/m1.jpg') }}'" /></a>
              </div>
              <div class="col-md-8 single-right-grid-right">
                <a href="{{ route('movies.show', $similar->id) }}" class="title">{{ $similar->title }}</a>
                <p class="author"><a href="#" class="author">{{ $similar->year }}</a></p>
                <p class="views" style="text-align: justify; margin-right: 10px">{{ $similar->sinopsis }}</p>
              </div>
              <div class="clearfix"> </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="clearfix"> </div>
    </div>
      <!-- //movie-browse-agile -->
      <!--body wrapper start-->
    <div class="w3_agile_banner_bottom_grid">
      <div id="owl-demo" class="owl-carousel owl-theme">
        @foreach ($filmByRelease as $filmRelease)
          <div class="item">
            <div class="w3l-movie-gride-agile w3l-movie-gride-agile1">
              @php
                $releasePoster = $filmRelease->poster 
                  ? asset(str_starts_with($filmRelease->poster, 'storage/') ? $filmRelease->poster : 'storage/' . $filmRelease->poster)
                  : asset('template/images/m1.jpg');
              @endphp
              <a href="{{ route('movies.show', $filmRelease->id) }}" class="hvr-shutter-out-horizontal"><img src="{{ $releasePoster }}" title="{{ $filmRelease->title }}" class="img-responsive" alt="{{ $filmRelease->title }}" onerror="this.src='{{ asset('template/images/m1.jpg') }}'" />
                <div class="w3l-action-icon"><i class="fa fa-play-circle" aria-hidden="true"></i></div>
              </a>
              <div class="mid-1 agileits_w3layouts_mid_1_home">
                <div class="w3l-movie-text">
                  <h6><a href="{{ route('movies.show', $filmRelease->id) }}">{{ $filmRelease->title }}</a></h6>							
                </div>
                <div class="mid-2 agile_mid_2_home">
                  <p>{{ $filmRelease->year }}</p>
                  <div class="block-stars">
                    <ul class="w3l-ratings">
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star-half-o" aria-hidden="true"></i></a></li>
                    </ul>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
              @if ($filmRelease->year == now()->year)
                <div class="ribben">
                  <p>NEW</p>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  <!--body wrapper end-->
          
             
      </div>
      <!-- //w3l-latest-movies-grids -->
    </div>	
  </div>
@endsection

@push('scripts')
<script src="js/simplePlayer.js"></script>
<script>
	$("document").ready(function() {
		$("#video").simplePlayer();
	});
</script>
@endpush