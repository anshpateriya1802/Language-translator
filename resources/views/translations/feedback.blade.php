@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header bg-gradient-primary text-white">
        <h2 class="mb-0">{{ __('Feedback Center') }}</h2>
    </div>
    
    <div class="card-body">
        @if($feedbacks->isEmpty())
            <div class="alert alert-info animate__animated animate__fadeIn">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('You haven\'t provided any feedback on translations yet.') }}
                <a href="{{ route('translations.index') }}">{{ __('View your translations') }}</a> to rate them.
            </div>
        @else
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 animate__animated animate__fadeIn" style="animation-delay: 0.2s">
                        <div class="card-body text-center p-4">
                            <div class="display-4 mb-3">
                                <i class="fas fa-star text-warning"></i>
                                {{ number_format($feedbacks->avg('rating'), 1) }}
                            </div>
                            <h5>{{ __('Average Rating') }}</h5>
                            <div class="text-muted small">{{ __('Based on') }} {{ $feedbacks->count() }} {{ __('translations') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                        <div class="card-body text-center p-4">
                            <div class="display-4 mb-3">
                                <i class="fas fa-comment-dots text-primary"></i>
                                {{ $feedbacks->whereNotNull('feedback')->count() }}
                            </div>
                            <h5>{{ __('Feedback Comments') }}</h5>
                            <div class="text-muted small">
                                {{ __('From') }} {{ $feedbacks->count() }} {{ __('rated translations') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <h4 class="border-bottom pb-2 mb-3 animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                <i class="fas fa-comments me-2"></i>{{ __('Your Feedback') }}
            </h4>
            
            <div class="accordion feedback-accordion mb-4 animate__animated animate__fadeIn" style="animation-delay: 0.5s">
                @foreach($feedbacks as $index => $feedback)
                    <div class="accordion-item border-0 shadow-sm mb-3" style="border-radius: var(--border-radius); overflow: hidden;">
                        <h2 class="accordion-header" id="heading{{ $feedback->id }}">
                            <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $feedback->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $feedback->id }}">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $feedback->sourceLanguage->name }}</span>
                                        <i class="fas fa-arrow-right text-muted mx-1"></i>
                                        <span class="badge bg-success me-2">{{ $feedback->targetLanguage->name }}</span>
                                        <strong>{{ Str::limit($feedback->source_text, 40) }}</strong>
                                    </div>
                                    <div class="ms-auto text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedback->rating ? '' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $feedback->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $feedback->id }}" data-bs-parent=".feedback-accordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-0 mb-3">
                                            <div class="card-header bg-light">
                                                <strong>{{ __('Original Text') }}</strong>
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $feedback->source_text }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 mb-3">
                                            <div class="card-header bg-light">
                                                <strong>{{ __('Translation') }}</strong>
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $feedback->translated_text }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Your Feedback') }}</h5>
                                        @if($feedback->feedback)
                                            <p class="card-text">{{ $feedback->feedback }}</p>
                                        @else
                                            <p class="card-text text-muted">{{ __('No written feedback provided.') }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between mt-3">
                                            <span>
                                                <strong>{{ __('Rating') }}:</strong>
                                                <span class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $feedback->rating ? '' : 'text-muted' }}"></i>
                                                    @endfor
                                                </span>
                                            </span>
                                            <span class="text-muted small">
                                                {{ $feedback->updated_at->format('M d, Y H:i') }}
                                            </span>
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('translations.show', $feedback) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i>{{ __('Edit Feedback') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 animate__animated animate__fadeIn" style="animation-delay: 0.6s">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .accordion-button:not(.collapsed) {
        background-color: rgba(99, 102, 241, 0.1);
        color: var(--primary-color);
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(99, 102, 241, 0.1);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
    }
</style>
@endpush
@endsection 