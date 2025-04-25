@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header bg-gradient-primary text-white">
        <h2 class="mb-0">{{ __('Create Translation') }}</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('translate.store') }}">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6 animate__animated animate__fadeInLeft">
                    <label for="source_language_id" class="form-label">{{ __('Source Language') }}</label>
                    <select id="source_language_id" name="source_language_id" class="form-select @error('source_language_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Source Language') }}</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" {{ old('source_language_id') == $language->id ? 'selected' : '' }}>
                                {{ $language->name }} ({{ $language->native_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('source_language_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-md-6 animate__animated animate__fadeInRight">
                    <label for="target_language_id" class="form-label">{{ __('Target Language') }}</label>
                    <select id="target_language_id" name="target_language_id" class="form-select @error('target_language_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Target Language') }}</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" {{ old('target_language_id') == $language->id ? 'selected' : '' }}>
                                {{ $language->name }} ({{ $language->native_name }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        You can select the same language as source to get explanations for idioms.
                    </small>
                    @error('target_language_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div id="same-language-alert" class="alert alert-info mt-2 d-none">
                        <i class="fas fa-info-circle me-1"></i> When source and target languages are the same, the system will provide explanations for idioms rather than translations.
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                    <label for="source_text" class="form-label">{{ __('Text to Translate') }}</label>
                    <textarea id="source_text" name="source_text" rows="5" class="form-control @error('source_text') is-invalid @enderror" required>{{ old('source_text') }}</textarea>
                    @error('source_text')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6 animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                    <label for="region_id" class="form-label">{{ __('Region (Optional)') }}</label>
                    <select id="region_id" name="region_id" class="form-select @error('region_id') is-invalid @enderror">
                        <option value="">{{ __('No Specific Region') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }} ({{ $region->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="col-md-6 animate__animated animate__fadeIn" style="animation-delay: 0.5s">
                    <label for="context" class="form-label">{{ __('Context (Optional)') }}</label>
                    <input type="text" id="context" name="context" class="form-control @error('context') is-invalid @enderror" value="{{ old('context') }}">
                    @error('context')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12 animate__animated animate__fadeIn" style="animation-delay: 0.6s">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="contains_idioms" id="contains_idioms" value="1" {{ old('contains_idioms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="contains_idioms">
                            {{ __('This text contains idioms or regional expressions') }}
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between animate__animated animate__fadeIn" style="animation-delay: 0.7s">
                <a href="{{ route('translations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Translations') }}
                </a>
                <button type="submit" class="btn btn-primary animate__animated animate__pulse animate__infinite animate__slow">
                    <i class="fas fa-language me-1"></i>{{ __('Translate Now') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .form-control, .form-select {
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        transform: scale(1.01);
        box-shadow: 0 0 10px rgba(78, 115, 223, 0.2);
    }
    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sourceLanguage = document.getElementById('source_language_id');
        const targetLanguage = document.getElementById('target_language_id');
        const sameLanguageAlert = document.getElementById('same-language-alert');
        const idiomsCheckbox = document.getElementById('contains_idioms');
        
        function checkLanguages() {
            if (sourceLanguage.value && targetLanguage.value && sourceLanguage.value === targetLanguage.value) {
                sameLanguageAlert.classList.remove('d-none');
                idiomsCheckbox.checked = true;
            } else {
                sameLanguageAlert.classList.add('d-none');
            }
        }
        
        sourceLanguage.addEventListener('change', checkLanguages);
        targetLanguage.addEventListener('change', checkLanguages);
        
        // Check on page load for pre-filled values
        checkLanguages();
    });
</script>
@endpush
@endsection 