@extends('admin.layouts.app')

@section('title', 'Tambah Film')

@section('content')
<div class="content-header">
  <h1><i class="fa fa-plus-circle"></i> Tambah Film Baru</h1>
</div>

<div class="panel panel-default">
  <div class="panel-heading" style="background:#fff; font-weight:bold;">
    Form Tambah Film
  </div>
  <div class="panel-body">
    <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="form-group @error('title') has-error @enderror">
        <label for="title">Judul Film <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Masukkan judul film" required>
        @error('title')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group @error('year') has-error @enderror">
        <label for="year">Tahun Rilis <span class="text-danger">*</span></label>
        <input type="number" name="year" id="year" class="form-control" style="max-width: 200px;" value="{{ old('year', date('Y')) }}" min="1900" max="{{ (int) date('Y') + 1 }}" placeholder="Contoh: 2024" required>
        @error('year')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group @error('genre_id') has-error @enderror">
        <label>Pilih Genre <span class="text-danger">*</span></label>
        <p class="text-muted" style="font-size: 12px; margin-bottom: 8px;">Pilih salah satu genre untuk film ini:</p>
        <div class="row" style="margin-left: 0; margin-right: 0; padding: 10px; background: #fafafa; border: 1px solid #e5e5e5; border-radius: 4px;">
          @foreach($genres as $genre)
            <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 8px;">
              <label class="checkbox-inline" style="font-weight: normal; cursor: pointer; display: flex; align-items: center;">
                <input type="checkbox" name="genre_id" value="{{ $genre->id }}" class="genre-checkbox"
                  {{ old('genre_id') == $genre->id ? 'checked' : '' }} style="margin-top: 0; margin-right: 6px;">
                <span>{{ $genre->name }}</span>
              </label>
            </div>
          @endforeach
        </div>
        @error('genre_id')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group @error('sinopsis') has-error @enderror">
        <label for="sinopsis">Sinopsis <span class="text-danger">*</span></label>
        <textarea name="sinopsis" id="sinopsis" rows="5" class="form-control" placeholder="Tulis sinopsis film..." required>{{ old('sinopsis') }}</textarea>
        @error('sinopsis')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group @error('poster') has-error @enderror">
        <label for="poster">Upload Poster <span class="text-danger">*</span></label>
        <input type="file" name="poster" id="poster" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
        <p class="help-block" style="font-size:12px;">Format: JPG, JPEG, PNG, WEBP. Ukuran maksimal 2048 KB (2 MB).</p>
        
        <!-- Live Preview Poster -->
        <div id="poster-preview-wrapper" style="display: none; margin-top: 10px;">
          <p class="text-muted" style="margin-bottom: 5px; font-size: 12px; font-weight: bold;">Preview Poster Langsung:</p>
          <img id="poster-preview" src="#" alt="Preview Poster" style="width: 120px; height: 170px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
        </div>

        @error('poster')
          <span class="help-block">{{ $message }}</span>
        @enderror
      </div>

      <hr style="margin: 20px 0;">

      <button type="submit" class="btn btn-orange">
        <i class="fa fa-save"></i> Simpan Film
      </button>
      <a href="{{ route('admin.films.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Batal
      </a>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Checkbox genre single-choice behavior
  document.querySelectorAll('.genre-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      if (this.checked) {
        document.querySelectorAll('.genre-checkbox').forEach(function(other) {
          if (other !== checkbox) other.checked = false;
        });
      }
    });
  });

  // Live preview upload poster
  document.getElementById('poster').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const wrapper = document.getElementById('poster-preview-wrapper');
    const img = document.getElementById('poster-preview');

    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        img.src = e.target.result;
        wrapper.style.display = 'block';
      };
      reader.readAsDataURL(file);
    } else {
      wrapper.style.display = 'none';
      img.src = '#';
    }
  });
</script>
@endpush

