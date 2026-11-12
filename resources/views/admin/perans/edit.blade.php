@extends('admin.layouts.app')

@section('title', 'Edit Peran')

@section('content')
<div class="content-header">
  <h1><i class="fa fa-edit"></i> Edit Peran (Pemeran Film)</h1>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background:#fff; font-weight:bold;">
    Form Edit Peran
  </div>
  <div class="panel-body">
    <form action="{{ route('admin.perans.update', $peran->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-md-6">
          <div class="form-group @error('film_id') has-error @enderror">
            <label for="film_id">Pilih Film <span class="text-danger">*</span></label>
            <select name="film_id" id="film_id" class="form-control" required>
              <option value="">-- Pilih Film --</option>
              @foreach($films as $film)
                <option value="{{ $film->id }}" {{ old('film_id', $peran->film_id) == $film->id ? 'selected' : '' }}>
                  {{ $film->title }} ({{ $film->year }})
                </option>
              @endforeach
            </select>
            @error('film_id')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group @error('cast_id') has-error @enderror">
            <label for="cast_id">Pilih Cast (Aktor) <span class="text-danger">*</span></label>
            <select name="cast_id" id="cast_id" class="form-control" required>
              <option value="">-- Pilih Cast --</option>
              @foreach($casts as $cast)
                <option value="{{ $cast->id }}" {{ old('cast_id', $peran->cast_id) == $cast->id ? 'selected' : '' }}>
                  {{ $cast->name }}
                </option>
              @endforeach
            </select>
            @error('cast_id')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-group @error('actor') has-error @enderror">
        <label for="actor">Nama Peran / Karakter <span class="text-danger">*</span></label>
        <input type="text" name="actor" id="actor" class="form-control" value="{{ old('actor', $peran->actor) }}" placeholder="Contoh: Tony Stark / Iron Man" required>
        @error('actor')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <hr style="margin: 20px 0;">

      <button type="submit" class="btn btn-orange">
        <i class="fa fa-save"></i> Perbarui Peran
      </button>
      <a href="{{ route('admin.perans.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Batal
      </a>
    </form>
  </div>
</div>
@endsection

