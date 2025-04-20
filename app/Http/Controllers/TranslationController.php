<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\Language;
use App\Models\Region;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    protected $translationService;
    
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    
    /**
     * Display a listing of the translations.
     */
    public function index()
    {
        $translations = Translation::where('user_id', Auth::id())
            ->with(['sourceLanguage', 'targetLanguage', 'region'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('translations.index', compact('translations'));
    }

    /**
     * Show the form for creating a new translation.
     */
    public function create()
    {
        $languages = Language::where('is_active', true)->get();
        $regions = Region::where('is_active', true)->get();
        
        return view('translations.create', compact('languages', 'regions'));
    }

    /**
     * Store a newly created translation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_text' => 'required|string',
            'source_language_id' => 'required|exists:languages,id',
            'target_language_id' => 'required|exists:languages,id|different:source_language_id',
            'region_id' => 'nullable|exists:regions,id',
            'context' => 'nullable|string',
            'contains_idioms' => 'boolean',
        ]);
        
        // Get the language codes for translation
        $sourceLanguage = Language::findOrFail($validated['source_language_id']);
        $targetLanguage = Language::findOrFail($validated['target_language_id']);
        
        // Make sure the contains_idioms field is properly set (checkboxes often default to null if not checked)
        $containsIdioms = isset($validated['contains_idioms']) ? (bool) $validated['contains_idioms'] : false;
        
        Log::debug('Translation request params', [
            'source_text' => $validated['source_text'],
            'source_language' => $sourceLanguage->name, 
            'target_language' => $targetLanguage->name,
            'contains_idioms' => $containsIdioms
        ]);
        
        // Translate the text using our service
        $translationResult = $this->translationService->translate(
            $validated['source_text'],
            $sourceLanguage->code,
            $targetLanguage->code,
            $containsIdioms
        );
        
        Log::debug('Translation result from service', $translationResult);
        
        // Create the translation record
        $translation = Translation::create([
            'source_text' => $validated['source_text'],
            'translated_text' => $translationResult['translated_text'],
            'source_language_id' => $validated['source_language_id'],
            'target_language_id' => $validated['target_language_id'],
            'region_id' => $validated['region_id'] ?? null,
            'user_id' => Auth::id(),
            'translation_method' => $translationResult['translation_method'],
            'context' => $validated['context'] ?? null,
            'contains_idioms' => $translationResult['contains_idioms'] ?? $containsIdioms,
        ]);
        
        return redirect()->route('translations.show', $translation)
            ->with('success', 'Translation created successfully!');
    }

    /**
     * Display the specified translation.
     */
    public function show(Translation $translation)
    {
        // Check if the translation belongs to the authenticated user
        if ($translation->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('translations.show', compact('translation'));
    }

    /**
     * Update the specified translation in storage.
     * This is used for updating feedback and ratings.
     */
    public function update(Request $request, Translation $translation)
    {
        // Check if the translation belongs to the authenticated user
        if ($translation->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        $translation->update($validated);
        
        return redirect()->route('translations.show', $translation)
            ->with('success', 'Feedback submitted successfully!');
    }

    /**
     * Show the form for editing the specified translation.
     */
    public function edit(string $id)
    {
        // Not implementing editing of translations for now
    }

    /**
     * Remove the specified translation from storage.
     */
    public function destroy(Translation $translation)
    {
        // Check if the translation belongs to the authenticated user
        if ($translation->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Delete the translation
        $translation->delete();
        
        return redirect()->route('translations.index')
            ->with('success', 'Translation deleted successfully.');
    }
    
    /**
     * Remove all translations for the authenticated user.
     */
    public function destroyAll()
    {
        // Delete all translations for the current user
        Translation::where('user_id', Auth::id())->delete();
        
        return redirect()->route('translations.index')
            ->with('success', 'All translations cleared successfully.');
    }
    
    /**
     * Display all feedback across translations.
     */
    public function feedback()
    {
        $feedbacks = Translation::where(function($query) {
                $query->whereNotNull('feedback')
                      ->orWhereNotNull('rating');
            })
            ->where('user_id', Auth::id())
            ->with(['sourceLanguage', 'targetLanguage', 'region'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
            
        return view('translations.feedback', compact('feedbacks'));
    }
}
