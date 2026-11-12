@extends('admin.layouts.app')

@section('title', 'Daftar Cast')

@section('content')
<div class="content-header">
  <div class="row">
    <div class="col-sm-6">
      <h1><i class="fa fa-users"></i> Kelola Cast (Aktor)</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.casts.create') }}" class="btn btn-orange">
        <i class="fa fa-plus"></i> Tambah Cast Baru
      </a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background: #fff; padding: 12px 15px;">
    <div class="row">
      <div class="col-md-6">
        <form method="GET" action="{{ route('admin.casts.index') }}" class="form-inline">
          <div class="input-group">
            <input type="text" name="search" class="form-control input-sm" placeholder="Cari nama cast..." value="{{ request('search') }}">
            <span class="input-group-btn">
              <button class="btn btn-default btn-sm" type="submit">
                <i class="fa fa-search"></i> Cari
              </button>
              @if(request('search'))
                <a href="{{ route('admin.casts.index') }}" class="btn btn-danger btn-sm" title="Reset Pencarian">
                  <i class="fa fa-times"></i>
                </a>
              @endif
            </span>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-right text-muted" style="padding-top: 5px; font-size: 13px;">
        Menampilkan {{ $casts->firstItem() ?? 0 }} - {{ $casts->lastItem() ?? 0 }} dari total {{ $casts->total() }} cast
      </div>
    </div>
  </div>

  <div class="panel-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table table-hover table-striped" style="margin-bottom: 0;">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">No</th>
            <th>Nama Cast</th>
            <th style="width: 80px; text-align: center;">Umur</th>
            <th>Bio</th>
            <th style="width: 130px; text-align: center;">Jumlah Peran</th>
            <th style="width: 160px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($casts as $index => $cast)
            <tr>
              <td style="vertical-align: middle; text-align: center;">
                {{ $casts->firstItem() + $index }}
              </td>
              <td style="vertical-align: middle;">
                <strong>{{ $cast->name }}</strong>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                {{ $cast->age }} th
              </td>
              <td style="vertical-align: middle;">
                <span title="{{ $cast->bio }}">{{ Str::limit($cast->bio, 80) }}</span>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <span class="badge" style="background-color: #00a65a;">{{ $cast->perans_count ?? $cast->perans()->count() }} Peran</span>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <a href="{{ route('admin.casts.edit', $cast->id) }}" class="btn btn-warning btn-xs" title="Edit Cast">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.casts.destroy', $cast->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cast ini?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs" title="Hapus Cast">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                @if(request('search'))
                  Tidak ada cast matching "<strong>{{ request('search') }}</strong>".
                @else
                  Belum ada data cast.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($casts->hasPages())
    <div class="panel-footer" style="background: #fff; text-align: center;">
      {{ $casts->links() }}
    </div>
  @endif
</div>
@endsection

