@extends('admin.layouts.app')

@section('title', 'Edit Genre')

@section('content')
<div class="content-header">
  <h1><i class="fa fa-edit"></i> Edit Genre</h1>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background:#fff; font-weight:bold;">
    Form Edit Genre
  </div>
  <div class="panel-body">
    <form action="{{ route('admin.genres.update', $genre->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group @error('name') has-error @enderror">
        <label for="name">Nama Genre <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $genre->name) }}" placeholder="Contoh: Action, Comedy, Drama" maxlength="45" required>
        @error('name')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <hr style="margin: 20px 0;">

      <button type="submit" class="btn btn-orange">
        <i class="fa fa-save"></i> Perbarui Genre
      </button>
      <a href="{{ route('admin.genres.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Batal
      </a>
    </form>
  </div>
</div>
@endsection

