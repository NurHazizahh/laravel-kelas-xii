@extends('admin.layouts.app')

@section('title', 'Tambah Cast')

@section('content')
<div class="content-header">
  <h1><i class="fa fa-user-plus"></i> Tambah Cast (Aktor) Baru</h1>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background:#fff; font-weight:bold;">
    Form Tambah Cast
  </div>
  <div class="panel-body">
    <form action="{{ route('admin.casts.store') }}" method="POST">
      @csrf

      <div class="row">
        <div class="col-md-8">
          <div class="form-group @error('name') has-error @enderror">
            <label for="name">Nama Cast <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Robert Downey Jr." maxlength="45" required>
            @error('name')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group @error('age') has-error @enderror">
            <label for="age">Umur (Tahun) <span class="text-danger">*</span></label>
            <input type="number" name="age" id="age" class="form-control" value="{{ old('age') }}" min="1" max="120" placeholder="1 - 120" required>
            @error('age')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-group @error('bio') has-error @enderror">
        <label for="bio">Biografi <span class="text-danger">*</span></label>
        <textarea name="bio" id="bio" rows="5" class="form-control" placeholder="Tulis biografi singkat tentang aktor..." required>{{ old('bio') }}</textarea>
        @error('bio')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <hr style="margin: 20px 0;">

      <button type="submit" class="btn btn-orange">
        <i class="fa fa-save"></i> Simpan Cast
      </button>
      <a href="{{ route('admin.casts.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Batal
      </a>
    </form>
  </div>
</div>
@endsection

