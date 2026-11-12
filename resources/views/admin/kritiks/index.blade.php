@extends('admin.layouts.app')

@section('title', 'Daftar Komentar')

@section('content')
<div class="content-header">
  <div class="row">
    <div class="col-sm-6">
      <h1><i class="fa fa-comments"></i> Kelola Komentar / Kritik</h1>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background: #fff; padding: 12px 15px;">
    <div class="row">
      <div class="col-md-6">
        <form method="GET" action="{{ route('admin.kritiks.index') }}" class="form-inline">
          <div class="input-group">
            <input type="text" name="search" class="form-control input-sm" placeholder="Cari isi komentar, user, atau film..." value="{{ request('search') }}">
            <span class="input-group-btn">
              <button class="btn btn-default btn-sm" type="submit">
                <i class="fa fa-search"></i> Cari
              </button>
              @if(request('search'))
                <a href="{{ route('admin.kritiks.index') }}" class="btn btn-danger btn-sm" title="Reset Pencarian">
                  <i class="fa fa-times"></i>
                </a>
              @endif
            </span>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-right text-muted" style="padding-top: 5px; font-size: 13px;">
        Menampilkan {{ $kritiks->firstItem() ?? 0 }} - {{ $kritiks->lastItem() ?? 0 }} dari total {{ $kritiks->total() }} komentar
      </div>
    </div>
  </div>

  <div class="panel-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table table-hover table-striped" style="margin-bottom: 0;">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">No</th>
            <th>Nama User</th>
            <th>Judul Film</th>
            <th style="width: 100px; text-align: center;">Rating</th>
            <th>Isi Komentar</th>
            <th style="width: 150px;">Tanggal</th>
            <th style="width: 100px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($kritiks as $index => $kritik)
            <tr>
              <td style="vertical-align: middle; text-align: center;">
                {{ $kritiks->firstItem() + $index }}
              </td>
              <td style="vertical-align: middle;">
                <strong><i class="fa fa-user"></i> {{ $kritik->user?->name ?? 'Pengguna dihapus' }}</strong>
              </td>
              <td style="vertical-align: middle;">
                <span class="label label-default" style="font-size: 12px;">{{ $kritik->film->title ?? 'Film #' . $kritik->film_id }}</span>
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <span class="label label-warning" style="font-size: 12px;">
                  <i class="fa fa-star"></i> {{ $kritik->rating }} / 10
                </span>
              </td>
              <td style="vertical-align: middle;">
                <span>{{ Str::limit($kritik->comment, 80) }}</span>
              </td>
              <td style="vertical-align: middle; font-size: 12px; color: #666;">
                {{ $kritik->created_at ? $kritik->created_at->format('d M Y H:i') : '-' }}
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <form action="{{ route('admin.kritiks.destroy', $kritik->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs" title="Hapus Komentar">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                @if(request('search'))
                  Tidak ada komentar matching "<strong>{{ request('search') }}</strong>".
                @else
                  Belum ada data komentar / kritik.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($kritiks->hasPages())
    <div class="panel-footer" style="background: #fff; text-align: center;">
      {{ $kritiks->links() }}
    </div>
  @endif
</div>
@endsection

