@extends('admin.layouts.app')

@section('title', 'Daftar Genre')

@section('content')
<div class="content-header">
  <div class="row">
    <div class="col-sm-6">
      <h1><i class="fa fa-tags"></i> Kelola Genre</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.genres.create') }}" class="btn btn-orange">
        <i class="fa fa-plus"></i> Tambah Genre Baru
      </a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background: #fff; padding: 12px 15px;">
    <div class="row">
      <div class="col-md-6">
        <form method="GET" action="{{ route('admin.genres.index') }}" class="form-inline">
          <div class="input-group">
            <input type="text" name="search" class="form-control input-sm" placeholder="Cari nama genre..." value="{{ request('search') }}">
            <span class="input-group-btn">
              <button class="btn btn-default btn-sm" type="submit">
                <i class="fa fa-search"></i> Cari
              </button>
              @if(request('search'))
                <a href="{{ route('admin.genres.index') }}" class="btn btn-danger btn-sm" title="Reset Pencarian">
                  <i class="fa fa-times"></i>
                </a>
              @endif
            </span>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-right text-muted" style="padding-top: 5px; font-size: 13px;">
        Menampilkan {{ $genres->firstItem() ?? 0 }} - {{ $genres->lastItem() ?? 0 }} dari total {{ $genres->total() }} genre
      </div>
    </div>
  </div>

  <div class="panel-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table table-hover table-striped" style="margin-bottom: 0;">
        <thead>
          <tr>
            <th style="width: 60px; text-align: center;">No</th>
            <th>Nama Genre</th>
            <th style="width: 150px; text-align: center;">Jumlah Film</th>
            <th style="width: 160px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($genres as $index => $genre)
            <tr>
              <td style="vertical-align: middle; text-align: center;">
                {{ $genres->firstItem() + $index }}
              </td>
              <td style="vertical-align: middle;">
                <strong>{{ $genre->name }}</strong>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <span class="badge" style="background-color: #00c0ef;">{{ $genre->films_count ?? $genre->films()->count() }} Film</span>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <a href="{{ route('admin.genres.edit', $genre->id) }}" class="btn btn-warning btn-xs" title="Edit Genre">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.genres.destroy', $genre->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus genre ini?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs" title="Hapus Genre">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted" style="padding: 30px;">
                @if(request('search'))
                  Tidak ada genre matching "<strong>{{ request('search') }}</strong>".
                @else
                  Belum ada data genre.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($genres->hasPages())
    <div class="panel-footer" style="background: #fff; text-align: center;">
      {{ $genres->links() }}
    </div>
  @endif
</div>
@endsection

