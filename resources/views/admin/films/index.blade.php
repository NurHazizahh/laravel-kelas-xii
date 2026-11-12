@extends('admin.layouts.app')

@section('title', 'Daftar Film')

@section('content')
<div class="content-header">
  <div class="row">
    <div class="col-sm-6">
      <h1><i class="fa fa-film"></i> Kelola Film</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.films.create') }}" class="btn btn-orange">
        <i class="fa fa-plus"></i> Tambah Film Baru
      </a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background: #fff; padding: 12px 15px;">
    <div class="row">
      <div class="col-md-6">
        <form method="GET" action="{{ route('admin.films.index') }}" class="form-inline">
          <div class="input-group">
            <input type="text" name="search" class="form-control input-sm" placeholder="Cari judul film..." value="{{ request('search') }}">
            <span class="input-group-btn">
              <button class="btn btn-default btn-sm" type="submit">
                <i class="fa fa-search"></i> Cari
              </button>
              @if(request('search'))
                <a href="{{ route('admin.films.index') }}" class="btn btn-danger btn-sm" title="Reset Pencarian">
                  <i class="fa fa-times"></i>
                </a>
              @endif
            </span>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-right text-muted" style="padding-top: 5px; font-size: 13px;">
        Menampilkan {{ $films->firstItem() ?? 0 }} - {{ $films->lastItem() ?? 0 }} dari total {{ $films->total() }} film
      </div>
    </div>
  </div>

  <div class="panel-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table table-hover table-striped" style="margin-bottom: 0;">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">No</th>
            <th style="width: 70px;">Poster</th>
            <th>Judul</th>
            <th>Tahun</th>
            <th>Genre</th>
            <th style="width: 150px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($films as $index => $film)
            <tr>
              <td style="vertical-align: middle; text-align: center;">
                {{ $films->firstItem() + $index }}
              </td>
              <td style="vertical-align: middle;">
                @php
                  $posterUrl = $film->poster 
                    ? asset(str_starts_with($film->poster, 'storage/') ? $film->poster : 'storage/' . $film->poster)
                    : asset('template/images/m1.jpg');
                @endphp
                <img src="{{ $posterUrl }}" alt="{{ $film->title }}" class="table-poster" onerror="this.src='{{ asset('template/images/m1.jpg') }}'">
              </td>
              <td style="vertical-align: middle;">
                <strong>{{ $film->title }}</strong>
                <p class="text-muted" style="margin: 0; font-size: 12px;">{{ Str::limit($film->sinopsis, 90) }}</p>
              </td>
              <td style="vertical-align: middle;">{{ $film->year }}</td>
              <td style="vertical-align: middle;">
                <span class="label label-info">{{ $film->genre->name ?? '-' }}</span>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-warning btn-xs" title="Edit Film">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.films.destroy', $film->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus film ini? Data peran dan komentar terkait juga akan dihapus.');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs" title="Hapus Film">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                @if(request('search'))
                  Tidak ada film dengan judul matching "<strong>{{ request('search') }}</strong>".
                @else
                  Belum ada data film.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($films->hasPages())
    <div class="panel-footer" style="background: #fff; text-align: center;">
      {{ $films->links() }}
    </div>
  @endif
</div>
@endsection

