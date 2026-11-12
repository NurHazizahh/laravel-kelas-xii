@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
  <h1><i class="fa fa-dashboard"></i> Dashboard Admin</h1>
</div>

<!-- Summary Cards -->
<div class="row">
  <div class="col-md-4 col-sm-6">
    <div class="stat-card" style="border-left-color: #FF8D1B;">
      <div class="stat-icon" style="color: #FF8D1B;"><i class="fa fa-film"></i></div>
      <div>
        <div class="stat-num">{{ $totalFilms }}</div>
        <div class="stat-label">Total Film</div>
      </div>
    </div>
  </div>

  <div class="col-md-4 col-sm-6">
    <div class="stat-card" style="border-left-color: #00c0ef;">
      <div class="stat-icon" style="color: #00c0ef;"><i class="fa fa-tags"></i></div>
      <div>
        <div class="stat-num">{{ $totalGenres }}</div>
        <div class="stat-label">Total Genre</div>
      </div>
    </div>
  </div>

  <div class="col-md-4 col-sm-6">
    <div class="stat-card" style="border-left-color: #00a65a;">
      <div class="stat-icon" style="color: #00a65a;"><i class="fa fa-users"></i></div>
      <div>
        <div class="stat-num">{{ $totalCasts }}</div>
        <div class="stat-label">Total Cast</div>
      </div>
    </div>
  </div>

  <div class="col-md-4 col-sm-6">
    <div class="stat-card" style="border-left-color: #f39c12;">
      <div class="stat-icon" style="color: #f39c12;"><i class="fa fa-user"></i></div>
      <div>
        <div class="stat-num">{{ $totalUsers }}</div>
        <div class="stat-label">Total Pengguna</div>
      </div>
    </div>
  </div>

  <div class="col-md-4 col-sm-6">
    <div class="stat-card" style="border-left-color: #605ca8;">
      <div class="stat-icon" style="color: #605ca8;"><i class="fa fa-id-badge"></i></div>
      <div>
        <div class="stat-num">{{ $totalPerans }}</div>
        <div class="stat-label">Total Peran</div>
      </div>
    </div>
  </div>

  <div class="col-md-4 col-sm-6">
    <div class="stat-card" style="border-left-color: #dd4b39;">
      <div class="stat-icon" style="color: #dd4b39;"><i class="fa fa-comments"></i></div>
      <div>
        <div class="stat-num">{{ $totalKritiks }}</div>
        <div class="stat-label">Total Komentar</div>
      </div>
    </div>
  </div>
</div>

<!-- Latest 5 Films Table -->
<div class="panel panel-default">
  <div class="panel-heading" style="background:#fff; font-weight:bold; font-size:16px;">
    <i class="fa fa-clock-o"></i> 5 Film Terbaru Ditambahkan
    <a href="{{ route('admin.films.index') }}" class="btn btn-default btn-xs pull-right">
      Lihat Semua Film &raquo;
    </a>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-responsive">
      <table class="table table-hover table-striped" style="margin-bottom:0;">
        <thead>
          <tr>
            <th style="width: 70px;">Poster</th>
            <th>Judul</th>
            <th>Genre</th>
            <th>Tahun</th>
            <th>Tanggal Dibuat</th>
            <th style="width: 100px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($latestFilms as $film)
            <tr>
              <td>
                @php
                  $dashPoster = $film->poster 
                    ? asset(str_starts_with($film->poster, 'storage/') ? $film->poster : 'storage/' . $film->poster)
                    : asset('template/images/m1.jpg');
                @endphp
                <img src="{{ $dashPoster }}" alt="{{ $film->title }}" class="table-poster" onerror="this.src='{{ asset('template/images/m1.jpg') }}'">
              </td>
              <td style="vertical-align: middle;">
                <strong>{{ $film->title }}</strong>
                <p class="text-muted" style="margin:0; font-size:12px;">{{ Str::limit($film->sinopsis, 80) }}</p>
              </td>
              <td style="vertical-align: middle;">
                <span class="label label-info">{{ $film->genre->name ?? '-' }}</span>
              </td>
              <td style="vertical-align: middle;">{{ $film->year }}</td>
              <td style="vertical-align: middle;">{{ $film->created_at ? $film->created_at->format('d M Y H:i') : '-' }}</td>
              <td style="vertical-align: middle;">
                <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-warning btn-xs">
                  <i class="fa fa-edit"></i> Edit
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted" style="padding: 20px;">Belum ada data film.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

