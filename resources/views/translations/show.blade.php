@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header d-flex justify-content-between align-items-center bg-gradient-primary text-white">
        <h2 class="mb-0">{{ __('Translation Details') }}</h2>
        <div>
            <a href="{{ route('translations.feedback') }}" class="btn btn-warning">
                <i class="fas fa-star me-1"></i>{{ __('View All Feedback') }}
            </a>
            <form action="{{ route('translations.destroy', $translation) }}" method="POST" class="d-inline ms-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger animate__animated animate__fadeIn" 
                        onclick="return confirm('{{ __('Are you sure you want to delete this translation?') }}')">
                    <i class="fas fa-trash me-1"></i>{{ __('Delete Translation') }}
                </button>
            </form>
            <a href="{{ route('translations.index') }}" class="btn btn-light ms-1 animate__animated animate__fadeIn">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Translations') }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6 animate__animated animate__fadeInLeft">
                <div class="card shadow-sm h-100 translation-card">
                    <div class="card-header bg-primary text-white">
                        <strong>{{ $translation->sourceLanguage->name }}</strong> 
                        ({{ $translation->sourceLanguage->native_name }})
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $translation->source_text }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 animate__animated animate__fadeInRight">
                <div class="card shadow-sm h-100 translation-card">
                    <div class="card-header bg-success text-white">
                        <strong>{{ $translation->targetLanguage->name }}</strong>
                        ({{ $translation->targetLanguage->native_name }})
                    </div>
                    <div class="card-body">
                        @if($translation->contains_idioms)
                            <div class="alert alert-info mb-2 animate__animated animate__pulse">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('This translation contains idioms or regional expressions.') }}
                            </div>
                        @endif
                        <p class="card-text">{{ $translation->translated_text }}</p>
                        @if($translation->translation_method === 'idiom_database' && strpos($translation->translated_text, '(') !== false)
                            <hr>
                            <p class="card-text text-muted">
                                <small>
                                    <strong>{{ __('Meaning') }}:</strong> 
                                    {{ preg_replace('/^(.*?) \((.*)\)$/', '$2', $translation->translated_text) }}
                                </small>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">{{ __('Additional Information') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        @if($translation->region)
                                            <tr class="animate__animated animate__fadeIn" style="animation-delay: 0.35s">
                                                <th width="35%" class="bg-light">{{ __('Region') }}</th>
                                                <td>{{ $translation->region->name }} ({{ $translation->region->country }})</td>
                                            </tr>
                                        @endif
                                        
                                        @if($translation->context)
                                            <tr class="animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                                                <th class="bg-light">{{ __('Context') }}</th>
                                                <td>{{ $translation->context }}</td>
                                            </tr>
                                        @endif
                                        
                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: 0.45s">
                                            <th class="bg-light">{{ __('Contains Idioms') }}</th>
                                            <td>
                                                @if($translation->contains_idioms)
                                                    <span class="badge bg-primary">{{ __('Yes') }}</span>
                                                @else
                                                    {{ __('No') }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: 0.5s">
                                            <th width="35%" class="bg-light">{{ __('Translation Method') }}</th>
                                            <td>
                                                @if($translation->translation_method === 'idiom_database')
                                                    <span class="badge bg-success">{{ __('Idiom Database') }}</span>
                                                @elseif($translation->translation_method === 'google_api')
                                                    <span class="badge bg-info">{{ __('Google Translate') }}</span>
                                                @elseif($translation->translation_method === 'fallback_dictionary')
                                                    <span class="badge bg-warning text-dark">{{ __('Dictionary Lookup') }}</span>
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $translation->translation_method)) }}
                                                @endif
                                            </td>
                                        </tr>
                                        
                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: 0.55s">
                                            <th class="bg-light">{{ __('Created At') }}</th>
                                            <td>{{ $translation->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        
                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: 0.6s">
                                            <th class="bg-light">{{ __('Last Updated') }}</th>
                                            <td>{{ $translation->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 animate__animated animate__fadeIn" style="animation-delay: 0.7s">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">{{ __('Rate Translation') }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('translations.update', $translation) }}" class="mb-3">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="rating" class="form-label">{{ __('Rating') }}</label>
                                    <div class="rating-container">
                                        <div class="rating">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $translation->rating == $i ? 'checked' : '' }}>
                                                <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="feedback" class="form-label">{{ __('Feedback') }}</label>
                                    <textarea name="feedback" id="feedback" rows="4" class="form-control">{{ $translation->feedback }}</textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary animate__animated animate__pulse">
                                <i class="fas fa-paper-plane me-1"></i>{{ __('Submit Feedback') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .translation-card {
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .translation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.05);
        transition: all 0.3s ease;
    }
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        transition: all 0.3s ease;
    }
    .badge:hover {
        transform: scale(1.1);
    }
    
    /* Star Rating Styles */
    .rating-container {
        display: flex;
        align-items: center;
        margin-top: 0.5rem;
    }
    .rating {
        display: flex;
        flex-direction: row-reverse;
        font-size: 1.5rem;
    }
    .rating input {
        display: none;
    }
    .rating label {
        color: #ddd;
        cursor: pointer;
        padding: 0 0.2rem;
    }
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
        color: #f8c125;
    }
</style>
@endpush
@endsection 