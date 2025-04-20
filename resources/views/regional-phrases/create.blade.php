@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>{{ __('Add Regional Phrase') }}</h2>
        <a href="{{ route('regional-phrases.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
    </div>
    
    <div class="card-body">
        <form method="POST" action="{{ route('regional-phrases.store') }}">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
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
                
                <div class="col-md-6">
                    <label for="target_language_id" class="form-label">{{ __('Target Language') }}</label>
                    <select id="target_language_id" name="target_language_id" class="form-select @error('target_language_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Target Language') }}</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" {{ old('target_language_id') == $language->id ? 'selected' : '' }}>
                                {{ $language->name }} ({{ $language->native_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('target_language_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="region_id" class="form-label">{{ __('Region') }}</label>
                    <select id="region_id" name="region_id" class="form-select @error('region_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Region') }}</option>
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
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="source_phrase" class="form-label">{{ __('Source Phrase') }}</label>
                    <textarea id="source_phrase" name="source_phrase" rows="3" class="form-control @error('source_phrase') is-invalid @enderror" required>{{ old('source_phrase') }}</textarea>
                    @error('source_phrase')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="translation" class="form-label">{{ __('Translation') }}</label>
                    <textarea id="translation" name="translation" rows="3" class="form-control @error('translation') is-invalid @enderror" required>{{ old('translation') }}</textarea>
                    @error('translation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="context" class="form-label">{{ __('Context (Optional)') }}</label>
                    <textarea id="context" name="context" rows="2" class="form-control @error('context') is-invalid @enderror">{{ old('context') }}</textarea>
                    <div class="form-text">{{ __('When is this phrase typically used?') }}</div>
                    @error('context')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input @error('is_idiom') is-invalid @enderror" type="checkbox" name="is_idiom" id="is_idiom" value="1" {{ old('is_idiom') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_idiom">
                            {{ __('This is an idiom') }}
                        </label>
                        <div class="form-text">{{ __('An expression whose meaning is not predictable from the usual meanings of its constituent elements') }}</div>
                        @error('is_idiom')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input @error('is_slang') is-invalid @enderror" type="checkbox" name="is_slang" id="is_slang" value="1" {{ old('is_slang') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_slang">
                            {{ __('This is slang') }}
                        </label>
                        <div class="form-text">{{ __('Very informal language that is usually spoken rather than written') }}</div>
                        @error('is_slang')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save Phrase') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 