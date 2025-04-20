@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>{{ __('Regional Phrase Details') }}</h2>
        <div>
            <a href="{{ route('regional-phrases.edit', $regionalPhrase) }}" class="btn btn-primary me-2">{{ __('Edit') }}</a>
            <a href="{{ route('regional-phrases.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>{{ $regionalPhrase->sourceLanguage->name }}</strong> 
                        ({{ $regionalPhrase->sourceLanguage->native_name }})
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $regionalPhrase->source_phrase }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>{{ $regionalPhrase->targetLanguage->name }}</strong>
                        ({{ $regionalPhrase->targetLanguage->native_name }})
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $regionalPhrase->translation }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <h4>{{ __('Additional Information') }}</h4>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="25%">{{ __('Region') }}</th>
                            <td>{{ $regionalPhrase->region->name }} ({{ $regionalPhrase->region->country }})</td>
                        </tr>
                        
                        @if($regionalPhrase->context)
                            <tr>
                                <th>{{ __('Context') }}</th>
                                <td>{{ $regionalPhrase->context }}</td>
                            </tr>
                        @endif
                        
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <td>
                                @if($regionalPhrase->is_idiom) <span class="badge bg-info me-2">{{ __('Idiom') }}</span> @endif
                                @if($regionalPhrase->is_slang) <span class="badge bg-warning me-2">{{ __('Slang') }}</span> @endif
                                @if(!$regionalPhrase->is_idiom && !$regionalPhrase->is_slang) {{ __('Regular phrase') }} @endif
                            </td>
                        </tr>
                        
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if($regionalPhrase->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <form method="POST" action="{{ route('regional-phrases.destroy', $regionalPhrase) }}" 
                      onsubmit="return confirm('{{ __('Are you sure you want to delete this phrase?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        {{ __('Delete Phrase') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 