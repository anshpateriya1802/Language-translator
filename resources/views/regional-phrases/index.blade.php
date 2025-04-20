@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>{{ __('Regional Phrases') }}</h2>
        <a href="{{ route('regional-phrases.create') }}" class="btn btn-primary">{{ __('Add New Phrase') }}</a>
    </div>
    
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('regional-phrases.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="language_id" class="form-label">{{ __('Language') }}</label>
                    <select id="language_id" name="language_id" class="form-select">
                        <option value="">{{ __('All Languages') }}</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" {{ request('language_id') == $language->id ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="region_id" class="form-label">{{ __('Region') }}</label>
                    <select id="region_id" name="region_id" class="form-select">
                        <option value="">{{ __('All Regions') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }} ({{ $region->country }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" name="is_idiom" id="is_idiom" value="1" {{ request()->boolean('is_idiom') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_idiom">
                            {{ __('Idioms Only') }}
                        </label>
                    </div>
                    
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" name="is_slang" id="is_slang" value="1" {{ request()->boolean('is_slang') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_slang">
                            {{ __('Slang Only') }}
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary ms-auto">{{ __('Filter') }}</button>
                </div>
            </div>
        </form>
        
        @if($regionalPhrases->isEmpty())
            <div class="alert alert-info">
                {{ __('No regional phrases found.') }}
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Source Language') }}</th>
                            <th>{{ __('Source Phrase') }}</th>
                            <th>{{ __('Target Language') }}</th>
                            <th>{{ __('Translation') }}</th>
                            <th>{{ __('Region') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regionalPhrases as $phrase)
                            <tr>
                                <td>{{ $phrase->sourceLanguage->name }}</td>
                                <td>{{ Str::limit($phrase->source_phrase, 40) }}</td>
                                <td>{{ $phrase->targetLanguage->name }}</td>
                                <td>{{ Str::limit($phrase->translation, 40) }}</td>
                                <td>{{ $phrase->region->name }}</td>
                                <td>
                                    @if($phrase->is_idiom) <span class="badge bg-info">{{ __('Idiom') }}</span> @endif
                                    @if($phrase->is_slang) <span class="badge bg-warning">{{ __('Slang') }}</span> @endif
                                </td>
                                <td>
                                    <a href="{{ route('regional-phrases.show', $phrase) }}" class="btn btn-sm btn-info">
                                        {{ __('View') }}
                                    </a>
                                    <a href="{{ route('regional-phrases.edit', $phrase) }}" class="btn btn-sm btn-primary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('regional-phrases.destroy', $phrase) }}" 
                                          class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $regionalPhrases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 