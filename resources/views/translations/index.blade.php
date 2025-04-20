@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header d-flex justify-content-between align-items-center bg-gradient-primary text-white">
        <h2 class="mb-0">{{ __('My Translations') }}</h2>
        <div>
            <a href="{{ route('translate') }}" class="btn btn-light animate__animated animate__pulse animate__infinite animate__slower">
                <i class="fas fa-plus-circle me-1"></i>{{ __('New Translation') }}
            </a>
            <a href="{{ route('translations.feedback') }}" class="btn btn-warning ms-2">
                <i class="fas fa-star me-1"></i>{{ __('View Feedback') }}
            </a>
            @if(!$translations->isEmpty())
                <form action="{{ route('translations.destroy-all') }}" method="POST" class="d-inline ms-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger animate__animated animate__fadeIn" 
                            onclick="return confirm('{{ __('Are you sure you want to clear all your translations? This action cannot be undone.') }}')">
                        <i class="fas fa-trash me-1"></i>{{ __('Clear All') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($translations->isEmpty())
            <div class="alert alert-info animate__animated animate__fadeIn">
                {{ __('You haven\'t made any translations yet.') }}
                <a href="{{ route('translate') }}">{{ __('Create your first translation.') }}</a>
            </div>
        @else
            <div class="table-responsive animate__animated animate__fadeIn">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Source Language') }}</th>
                            <th>{{ __('Target Language') }}</th>
                            <th>{{ __('Source Text') }}</th>
                            <th>{{ __('Translation') }}</th>
                            <th width="100">{{ __('Rating') }}</th>
                            <th width="120">{{ __('Date') }}</th>
                            <th width="170">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($translations as $translation)
                            <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $loop->index * 0.05 }}s">
                                <td>
                                    <span class="badge bg-primary">{{ $translation->sourceLanguage->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $translation->targetLanguage->name }}</span>
                                </td>
                                <td>
                                    {{ Str::limit($translation->source_text, 30) }}
                                </td>
                                <td>
                                    {{ Str::limit($translation->translated_text, 30) }}
                                </td>
                                <td class="text-center">
                                    @if($translation->rating)
                                        <span class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $translation->rating ? '' : 'text-muted' }}"></i>
                                            @endfor
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $translation->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('translations.show', $translation) }}" class="btn btn-sm btn-info text-white animate__animated animate__fadeIn">
                                        <i class="fas fa-eye me-1"></i>{{ __('View') }}
                                    </a>
                                    <form action="{{ route('translations.destroy', $translation) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger animate__animated animate__fadeIn" 
                                                onclick="return confirm('{{ __('Are you sure you want to delete this translation?') }}')">
                                            <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 animate__animated animate__fadeIn">
                {{ $translations->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.05);
        transition: all 0.3s ease;
    }
</style>
@endpush
@endsection 