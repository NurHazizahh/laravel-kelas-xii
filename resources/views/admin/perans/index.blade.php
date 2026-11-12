@extends('admin.layouts.app')

@section('title', 'Daftar Peran')

@section('content')
<div class="content-header">
  <div class="row">
    <div class="col-sm-6">
      <h1><i class="fa fa-id-badge"></i> Kelola Peran (Pemeran Film)</h1>
    </div>
    <div class="col-sm-6 text-right">
      <a href="{{ route('admin.perans.create') }}" class="btn btn-orange">
        <i class="fa fa-plus"></i> Tambah Peran Baru
      </a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background: #fff; padding: 12px 15px;">
    <div class="row">
      <div class="col-md-6">
        <form method="GET" action="{{ route('admin.perans.index') }}" class="form-inline">
          <div class="input-group">
            <input type="text" name="search" class="form-control input-sm" placeholder="Cari karakter, film, atau cast..." value="{{ request('search') }}">
            <span class="input-group-btn">
              <button class="btn btn-default btn-sm" type="submit">
                <i class="fa fa-search"></i> Cari
              </button>
              @if(request('search'))
                <a href="{{ route('admin.perans.index') }}" class="btn btn-danger btn-sm" title="Reset Pencarian">
                  <i class="fa fa-times"></i>
                </a>
              @endif
            </span>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-right text-muted" style="padding-top: 5px; font-size: 13px;">
        Menampilkan {{ $perans->firstItem() ?? 0 }} - {{ $perans->lastItem() ?? 0 }} dari total {{ $perans->total() }} peran
      </div>
    </div>
  </div>

  <div class="panel-body" style="padding: 0;">
    <div class="table-responsive">
      <table class="table table-hover table-striped" style="margin-bottom: 0;">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">No</th>
            <th>Judul Film</th>
            <th>Nama Cast (Aktor)</th>
            <th>Nama Peran / Karakter</th>
            <th style="width: 160px; text-align: center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($perans as $index => $peran)
            <tr>
              <td style="vertical-align: middle; text-align: center;">
                {{ $perans->firstItem() + $index }}
              </td>
              <td style="vertical-align: middle;">
                <strong>{{ $peran->film->title ?? '-' }}</strong>
              </td>
              <td style="vertical-align: middle;">
                <span class="label label-primary">{{ $peran->cast->name ?? '-' }}</span>
              </td>
              <td style="vertical-align: middle;">
                <i class="fa fa-user-circle text-muted"></i> {{ $peran->actor }}
              </td>
              <td style="vertical-align: middle; text-align: center;">
                <a href="{{ route('admin.perans.edit', $peran->id) }}" class="btn btn-warning btn-xs" title="Edit Peran">
                  <i class="fa fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.perans.destroy', $peran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran ini?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs" title="Hapus Peran">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted" style="padding: 30px;">
                @if(request('search'))
                  Tidak ada peran matching "<strong>{{ request('search') }}</strong>".
                @else
                  Belum ada data peran.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($perans->hasPages())
    <div class="panel-footer" style="background: #fff; text-align: center;">
      {{ $perans->links() }}
    </div>
  @endif
</div>
@endsection

