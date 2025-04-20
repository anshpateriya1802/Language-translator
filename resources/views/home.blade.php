@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h2 class="mb-4">{{ __('Welcome to Language Translator') }}</h2>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">{{ __('Translate Text') }}</h4>
                                </div>
                                <div class="card-body">
                                    <p>{{ __('Translate text between languages with support for regional variations and idiomatic expressions.') }}</p>
                                    <a href="{{ route('translate') }}" class="btn btn-primary">{{ __('Start Translating') }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h4 class="mb-0">{{ __('My Translation History') }}</h4>
                                </div>
                                <div class="card-body">
                                    <p>{{ __('View your previous translations, see the details, and provide feedback on translation quality.') }}</p>
                                    <a href="{{ route('translations.index') }}" class="btn btn-success">{{ __('View History') }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h4 class="mb-0">{{ __('Regional Phrases') }}</h4>
                                </div>
                                <div class="card-body">
                                    <p>{{ __('Explore regional phrases, idioms, and slang expressions from around the world.') }}</p>
                                    <a href="{{ route('regional-phrases.index') }}" class="btn btn-info">{{ __('Explore Phrases') }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h4 class="mb-0">{{ __('Contribute') }}</h4>
                                </div>
                                <div class="card-body">
                                    <p>{{ __('Add new regional phrases, idioms, or slang expressions to help others understand language variations.') }}</p>
                                    <a href="{{ route('regional-phrases.create') }}" class="btn btn-warning">{{ __('Add New Phrase') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
