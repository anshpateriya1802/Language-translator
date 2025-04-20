<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegionalPhrase;
use App\Models\Language;
use App\Models\Region;
use Illuminate\Support\Facades\Auth;

class RegionalPhraseController extends Controller
{
    /**
     * Display a listing of the regional phrases.
     */
    public function index(Request $request)
    {
        $query = RegionalPhrase::with(['sourceLanguage', 'targetLanguage', 'region']);
        
        // Apply filters if provided
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        
        if ($request->filled('language_id')) {
            $query->where(function($q) use ($request) {
                $q->where('source_language_id', $request->language_id)
                  ->orWhere('target_language_id', $request->language_id);
            });
        }
        
        if ($request->boolean('is_idiom', false)) {
            $query->where('is_idiom', true);
        }
        
        if ($request->boolean('is_slang', false)) {
            $query->where('is_slang', true);
        }
        
        $regionalPhrases = $query->where('is_active', true)
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        $regions = Region::where('is_active', true)->get();
        $languages = Language::where('is_active', true)->get();
        
        return view('regional-phrases.index', compact('regionalPhrases', 'regions', 'languages'));
    }

    /**
     * Show the form for creating a new regional phrase.
     */
    public function create()
    {
        $languages = Language::where('is_active', true)->get();
        $regions = Region::where('is_active', true)->get();
        
        return view('regional-phrases.create', compact('languages', 'regions'));
    }

    /**
     * Store a newly created regional phrase in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_phrase' => 'required|string',
            'translation' => 'required|string',
            'source_language_id' => 'required|exists:languages,id',
            'target_language_id' => 'required|exists:languages,id|different:source_language_id',
            'region_id' => 'required|exists:regions,id',
            'context' => 'nullable|string',
            'is_idiom' => 'boolean',
            'is_slang' => 'boolean',
        ]);
        
        $regionalPhrase = RegionalPhrase::create([
            'source_phrase' => $validated['source_phrase'],
            'translation' => $validated['translation'],
            'source_language_id' => $validated['source_language_id'],
            'target_language_id' => $validated['target_language_id'],
            'region_id' => $validated['region_id'],
            'context' => $validated['context'] ?? null,
            'is_idiom' => $validated['is_idiom'] ?? false,
            'is_slang' => $validated['is_slang'] ?? false,
            'is_active' => true,
        ]);
        
        return redirect()->route('regional-phrases.show', $regionalPhrase)
            ->with('success', 'Regional phrase created successfully!');
    }

    /**
     * Display the specified regional phrase.
     */
    public function show(RegionalPhrase $regionalPhrase)
    {
        return view('regional-phrases.show', compact('regionalPhrase'));
    }

    /**
     * Show the form for editing the specified regional phrase.
     */
    public function edit(RegionalPhrase $regionalPhrase)
    {
        $languages = Language::where('is_active', true)->get();
        $regions = Region::where('is_active', true)->get();
        
        return view('regional-phrases.edit', compact('regionalPhrase', 'languages', 'regions'));
    }

    /**
     * Update the specified regional phrase in storage.
     */
    public function update(Request $request, RegionalPhrase $regionalPhrase)
    {
        $validated = $request->validate([
            'source_phrase' => 'required|string',
            'translation' => 'required|string',
            'source_language_id' => 'required|exists:languages,id',
            'target_language_id' => 'required|exists:languages,id|different:source_language_id',
            'region_id' => 'required|exists:regions,id',
            'context' => 'nullable|string',
            'is_idiom' => 'boolean',
            'is_slang' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        $regionalPhrase->update($validated);
        
        return redirect()->route('regional-phrases.show', $regionalPhrase)
            ->with('success', 'Regional phrase updated successfully!');
    }

    /**
     * Remove the specified regional phrase from storage.
     */
    public function destroy(RegionalPhrase $regionalPhrase)
    {
        $regionalPhrase->update(['is_active' => false]);
        
        return redirect()->route('regional-phrases.index')
            ->with('success', 'Regional phrase deactivated successfully!');
    }
}
