<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TranslationService
{
    /**
     * LibreTranslate API endpoint
     *
     * @var string
     */
    protected $libreTranslateEndpoint;

    /**
     * LibreTranslate fallback endpoints
     *
     * @var array
     */
    protected $fallbackEndpoints;

    /**
     * Idiom Translation Service instance
     *
     * @var IdiomTranslationService
     */
    protected $idiomTranslationService;
    
    /**
     * Max text length to send to API in a single request
     * 
     * @var int
     */
    protected $maxChunkSize = 1000;

    /**
     * Create a new TranslationService instance.
     *
     * @param IdiomTranslationService $idiomTranslationService
     */
    public function __construct(IdiomTranslationService $idiomTranslationService = null)
    {
        // Initialize LibreTranslate endpoint
        $this->libreTranslateEndpoint = config('services.libre_translate.endpoint');
        $this->fallbackEndpoints = config('services.libre_translate.fallback_endpoints', [
            'https://translate.argosopentech.com/translate',
            'https://libretranslate.de/translate',
        ]);
        
        // Initialize idiom translation service
        $this->idiomTranslationService = $idiomTranslationService ?? new IdiomTranslationService();
    }

    /**
     * Translate text from source language to target language.
     *
     * @param string $text Text to translate
     * @param string $sourceLanguageCode Source language code (e.g., 'en')
     * @param string $targetLanguageCode Target language code (e.g., 'es')
     * @param bool $containsIdioms Whether the text is likely to contain idioms
     * @return array [translated_text, translation_method, idiom_data]
     */
    public function translate(string $text, string $sourceLanguageCode, string $targetLanguageCode, bool $containsIdioms = false): array
    {
        // Skip empty text
        if (empty(trim($text))) {
            return [
                'translated_text' => '',
                'translation_method' => 'none',
                'contains_idioms' => false,
                'is_idiom' => false
            ];
        }

        // Debug logging
        Log::debug('Translation request', [
            'text' => $text,
            'source' => $sourceLanguageCode,
            'target' => $targetLanguageCode,
            'contains_idioms' => $containsIdioms
        ]);
        
        // If source and target languages are the same, treat as explanation request
        $isSameLanguage = $sourceLanguageCode === $targetLanguageCode;
        if ($isSameLanguage) {
            return $this->explainText($text, $sourceLanguageCode, $containsIdioms);
        }
        
        $isIdiom = false;
        $idiomData = null;
        $translationMethod = 'libre_translate';

        // Always check for idioms if:
        // 1. User explicitly marked it as containing idioms
        // 2. It's a short text (more likely to be an idiom)
        $isShortText = strlen($text) < 100;
        $shouldCheckForIdioms = $containsIdioms || $isShortText;
        
        // First, always try to detect idioms regardless of target language
        // This ensures consistent idiom detection
        if ($shouldCheckForIdioms) {
            Log::debug('Checking for idioms', [
                'text_length' => strlen($text), 
                'user_marked_idiom' => $containsIdioms,
                'is_short_text' => $isShortText
            ]);
            
            // Try to detect if this is an idiom
            $idiomResult = $this->idiomTranslationService->translateIdiom($text, $sourceLanguageCode, $targetLanguageCode);
            
            if ($idiomResult !== null) {
                Log::debug('Found idiom translation', $idiomResult);
                return [
                    'translated_text' => $idiomResult['translation'],
                    'translation_method' => 'idiom_database',
                    'contains_idioms' => true,
                    'is_idiom' => true,
                    'idiom_data' => $idiomResult
                ];
            } else {
                Log::debug('No idiom found in direct text');
                
                // If no direct match but it might contain idioms, check the text more thoroughly
                if ($containsIdioms) {
                    $idiomInLongerText = $this->checkForIdiomsInLongerText($text, $sourceLanguageCode, $targetLanguageCode);
                    if ($idiomInLongerText !== null) {
                        Log::debug('Found idiom in longer text', $idiomInLongerText);
                        return $idiomInLongerText;
                    }
                    
                    // If the user marked it as containing idioms but we couldn't find any using our standard methods,
                    // try a more aggressive search - this ensures consistency across languages
                    Log::debug('Attempting aggressive idiom search for text marked as containing idioms');
                    $aggressiveSearchResult = $this->aggressiveIdiomSearch($text, $sourceLanguageCode, $targetLanguageCode);
                    if ($aggressiveSearchResult !== null) {
                        Log::debug('Found idiom through aggressive search', $aggressiveSearchResult);
                        return $aggressiveSearchResult;
                    }
                }
            }
        }

        // If not an idiom or idiom translation failed, try LibreTranslate API
        if ($this->libreTranslateEndpoint) {
            try {
                // For longer texts, split into chunks to avoid API limitations
                if (strlen($text) > $this->maxChunkSize) {
                    Log::debug('Text exceeds max chunk size, splitting into chunks', [
                        'text_length' => strlen($text),
                        'max_chunk_size' => $this->maxChunkSize
                    ]);
                    
                    return $this->translateLongText($text, $sourceLanguageCode, $targetLanguageCode, $containsIdioms);
                }
                
                Log::debug('Attempting LibreTranslate translation', [
                    'text' => $text, 
                    'endpoint' => $this->libreTranslateEndpoint
                ]);
                
                // LibreTranslate expects this format for the request
                $requestData = [
                    'q' => $text,
                    'source' => $sourceLanguageCode,
                    'target' => $targetLanguageCode,
                    'format' => 'text'
                ];
                
                // Make sure we're using the right headers for JSON request
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->timeout(20)->post($this->libreTranslateEndpoint, $requestData);
                
                // Log the raw request and response for debugging
                Log::debug('LibreTranslate request', [
                    'endpoint' => $this->libreTranslateEndpoint,
                    'data' => $requestData
                ]);
                
                if ($response->successful()) {
                    $result = $response->json();
                    Log::debug('LibreTranslate response', $result);
                    
                    if (isset($result['translatedText']) && !empty($result['translatedText'])) {
                        return [
                            'translated_text' => $result['translatedText'],
                            'translation_method' => 'libre_translate',
                            'contains_idioms' => $containsIdioms,
                            'is_idiom' => false
                        ];
                    } else {
                        Log::warning('LibreTranslate returned empty translation', [
                            'result' => $result,
                            'text' => $text
                        ]);
                    }
                } else {
                    Log::error('LibreTranslate API error', [
                        'status' => $response->status(),
                        'reason' => $response->reason(),
                        'body' => $response->body()
                    ]);
                    
                    // Try with different endpoint if we get a 403 or other error
                    if ($response->status() == 403 || $response->status() == 401 || $response->status() >= 500) {
                        Log::info('Attempting to use fallback LibreTranslate endpoints');
                        
                        // Try each fallback endpoint until one works
                        foreach ($this->fallbackEndpoints as $fallbackEndpoint) {
                            Log::debug('Trying fallback endpoint', ['endpoint' => $fallbackEndpoint]);
                            $altResponse = Http::withHeaders([
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                            ])->timeout(20)->post($fallbackEndpoint, $requestData);
                            
                            if ($altResponse->successful()) {
                                $altResult = $altResponse->json();
                                if (isset($altResult['translatedText']) && !empty($altResult['translatedText'])) {
                                    return [
                                        'translated_text' => $altResult['translatedText'],
                                        'translation_method' => 'libre_translate_fallback',
                                        'contains_idioms' => $containsIdioms,
                                        'is_idiom' => false
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('LibreTranslate API error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]);
            }
        } else {
            Log::warning('LibreTranslate endpoint not configured - check your settings');
        }

        // If API translation fails completely, try Google Translate API as a last resort
        try {
            $googleResult = $this->translateWithGoogleTranslateAPI($text, $sourceLanguageCode, $targetLanguageCode);
            if ($googleResult !== null) {
                return [
                    'translated_text' => $googleResult,
                    'translation_method' => 'google_translate_api',
                    'contains_idioms' => $containsIdioms,
                    'is_idiom' => false
                ];
            }
        } catch (\Exception $e) {
            Log::error('Google Translate API error: ' . $e->getMessage());
        }

        // Fallback: Use basic dictionary for common phrases/words
        Log::debug('Using fallback translation');
        $fallbackResult = $this->getFallbackTranslation($text, $sourceLanguageCode, $targetLanguageCode);
        
        return [
            'translated_text' => $fallbackResult,
            'translation_method' => 'fallback_dictionary',
            'contains_idioms' => $containsIdioms,
            'is_idiom' => false
        ];
    }

    /**
     * Special method for providing explanations when source and target languages are the same
     * 
     * @param string $text Text to explain
     * @param string $languageCode Language code
     * @param bool $containsIdioms Whether the text might contain idioms
     * @return array Explanation result
     */
    protected function explainText(string $text, string $languageCode, bool $containsIdioms): array
    {
        Log::debug('Explaining text in same language', [
            'text' => $text,
            'language' => $languageCode,
            'contains_idioms' => $containsIdioms
        ]);
        
        // Normalize text for comparison
        $normalizedText = strtolower(trim($text));
        
        // Always treat explanation requests as containing idioms
        $containsIdioms = true;
        
        // First check for exact match with known idioms
        $idiomResult = $this->idiomTranslationService->translateIdiom($text, $languageCode, $languageCode);
        if ($idiomResult !== null) {
            Log::debug('Found exact idiom match for explanation', $idiomResult);
            
            // Format as explanation
            if (isset($idiomResult['original_idiom'])) {
                $explanation = "\"" . $idiomResult['original_idiom'] . "\" means: " . $idiomResult['translation'];
            } else {
                $explanation = $idiomResult['translation'];
            }
            
            return [
                'translated_text' => $explanation,
                'translation_method' => 'idiom_explanation',
                'contains_idioms' => true,
                'is_idiom' => true,
                'idiom_data' => $idiomResult
            ];
        }
        
        // Use the common idioms list for direct matching
        $commonIdioms = $this->getCommonIdioms($languageCode);
        
        foreach ($commonIdioms as $idiom) {
            // Check if the user's text contains this exact idiom (case-insensitive)
            if (stripos($normalizedText, strtolower($idiom)) !== false) {
                $idiomResult = $this->idiomTranslationService->translateIdiom($idiom, $languageCode, $languageCode);
                if ($idiomResult !== null) {
                    // For explanations, we provide both the original text and the explanation
                    $explanation = "\"$idiom\" means: " . $idiomResult['translation'];
                    
                    return [
                        'translated_text' => $explanation,
                        'translation_method' => 'idiom_explanation',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, ['matched_idiom' => $idiom])
                    ];
                }
            }
        }
        
        // More detailed check for idioms in sentences
        // Use the same method as normal translation to ensure consistency
        $idiomInText = $this->checkForIdiomsInLongerText($text, $languageCode, $languageCode);
        if ($idiomInText !== null) {
            Log::debug('Found idiom in text for explanation', $idiomInText);
            
            // For same-language explanation, format differently
            $originalIdiom = $idiomInText['idiom_data']['original_idiom'] ?? 
                             $idiomInText['idiom_data']['original_phrase'] ?? 
                             $idiomInText['idiom_data']['original_sentence'] ?? 
                             $idiomInText['idiom_data']['matched_idiom'] ?? $text;
            
            // Format as explanation
            $explanation = "\"$originalIdiom\" means: " . $idiomInText['translated_text'];
            $idiomInText['translated_text'] = $explanation;
            
            return $idiomInText;
        }
        
        // If no idioms found but text matches a known pattern, try one more approach
        // This is a specific fix for idioms that might be missed by the other methods
        $result = $this->detectCommonIdiomaticPatterns($text, $languageCode);
        if ($result !== null) {
            return $result;
        }
        
        // If no idioms found, return the original text with a note
        return [
            'translated_text' => $text . ' (No idiomatic expression detected in this text)',
            'translation_method' => 'same_language',
            'contains_idioms' => false,
            'is_idiom' => false
        ];
    }

    /**
     * Detect common idiomatic patterns in text
     * This is a fallback method for catching idioms that might be missed
     * 
     * @param string $text Text to check
     * @param string $languageCode Language code
     * @return array|null Result if an idiom is found, null otherwise
     */
    protected function detectCommonIdiomaticPatterns(string $text, string $languageCode): ?array
    {
        // Handle only English for now
        if ($languageCode !== 'en') {
            return null;
        }
        
        $normalizedText = strtolower(trim($text));
        
        // Common patterns that might contain idioms
        $patterns = [
            // Common expressions with more flexible matching
            '/(?:what does|explain|meaning of|define)\s+["\']?([\w\s\'\-]+)["\']?\s+(?:mean|idiom)/i' => '$1',
            '/["\']?([\w\s\'\-]+)["\']?\s+(?:idiom|expression|meaning)/i' => '$1',
            '/(?:explain|define|meaning of)\s+["\']?([\w\s\'\-]+)["\']?/i' => '$1',
        ];
        
        foreach ($patterns as $pattern => $extraction) {
            if (preg_match($pattern, $text, $matches)) {
                $potentialIdiom = trim($matches[1]);
                
                // Look up this potential idiom
                $idiomResult = $this->idiomTranslationService->translateIdiom($potentialIdiom, $languageCode, $languageCode);
                if ($idiomResult !== null) {
                    $explanation = "\"$potentialIdiom\" means: " . $idiomResult['translation'];
                    
                    return [
                        'translated_text' => $explanation,
                        'translation_method' => 'idiom_explanation',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, ['matched_idiom' => $potentialIdiom])
                    ];
                }
                
                // If we found a pattern match but no idiom translation, continue checking
                // with other patterns before giving up
            }
        }
        
        // Check for direct idiom mentions without a pattern
        foreach ($this->getCommonIdioms($languageCode) as $idiom) {
            // More aggressive exact matching for explanations
            $idiomLower = strtolower($idiom);
            if ($normalizedText === $idiomLower || 
                $normalizedText === "$idiomLower?" ||
                $normalizedText === "the $idiomLower" ||
                stripos($normalizedText, $idiomLower) !== false) {
                
                $idiomResult = $this->idiomTranslationService->translateIdiom($idiom, $languageCode, $languageCode);
                if ($idiomResult !== null) {
                    $explanation = "\"$idiom\" means: " . $idiomResult['translation'];
                    
                    return [
                        'translated_text' => $explanation,
                        'translation_method' => 'idiom_explanation',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, ['matched_idiom' => $idiom])
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Check for idioms in longer text by breaking it down
     * 
     * @param string $text Long text to check for idioms
     * @param string $sourceLanguageCode Source language
     * @param string $targetLanguageCode Target language
     * @return array|null Translation result if idiom found, null otherwise
     */
    protected function checkForIdiomsInLongerText(string $text, string $sourceLanguageCode, string $targetLanguageCode): ?array 
    {
        // First try common idioms from our database directly in the text
        $commonIdioms = $this->getCommonIdioms($sourceLanguageCode);
        
        foreach ($commonIdioms as $idiom) {
            // Simple case-insensitive match without normalization
            // This is for quick direct matching first
            if (stripos($text, $idiom) !== false) {
                $idiomResult = $this->idiomTranslationService->translateIdiom($idiom, $sourceLanguageCode, $targetLanguageCode);
                if ($idiomResult !== null) {
                    // Replace just the idiom part or format for explanation based on language context
                    $isSameLanguage = $sourceLanguageCode === $targetLanguageCode;
                    
                    if ($isSameLanguage) {
                        $translatedText = "\"$idiom\" means: " . $idiomResult['translation'];
                    } else {
                        $translatedText = str_ireplace(
                            $idiom, 
                            $idiomResult['translation'], 
                            $text
                        );
                    }
                    
                    return [
                        'translated_text' => $translatedText,
                        'translation_method' => 'idiom_database_direct',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, ['matched_idiom' => $idiom])
                    ];
                }
            }
        }
        
        // Split text into smaller chunks to check for idioms
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        Log::debug('Checking for idioms in ' . count($sentences) . ' sentences');
        
        // Check each sentence for idioms
        foreach ($sentences as $sentence) {
            // Skip very short sentences as they're unlikely to contain idioms
            if (strlen($sentence) < 10) {
                continue;
            }
            
            $idiomResult = $this->idiomTranslationService->translateIdiom($sentence, $sourceLanguageCode, $targetLanguageCode);
            if ($idiomResult !== null) {
                // Found an idiom in this sentence, use it to replace that part in the original text
                $translatedText = str_replace($sentence, $idiomResult['translation'], $text);
                
                return [
                    'translated_text' => $translatedText,
                    'translation_method' => 'idiom_database_partial',
                    'contains_idioms' => true,
                    'is_idiom' => true,
                    'idiom_data' => array_merge($idiomResult, ['original_sentence' => $sentence])
                ];
            }
        }
        
        // If sentences don't contain idioms, try with phrases from text
        $words = explode(' ', $text);
        $maxPhraseLength = min(10, count($words)); // Check phrases of up to 10 words
        
        for ($phraseLength = $maxPhraseLength; $phraseLength >= 3; $phraseLength--) {
            for ($i = 0; $i <= count($words) - $phraseLength; $i++) {
                $phrase = implode(' ', array_slice($words, $i, $phraseLength));
                
                // Skip very short phrases
                if (strlen($phrase) < 10) {
                    continue;
                }
                
                $idiomResult = $this->idiomTranslationService->translateIdiom($phrase, $sourceLanguageCode, $targetLanguageCode);
                if ($idiomResult !== null) {
                    // Found an idiom in this phrase, use it to replace that part in the original text
                    $translatedText = str_replace($phrase, $idiomResult['translation'], $text);
                    
                    return [
                        'translated_text' => $translatedText,
                        'translation_method' => 'idiom_database_partial',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, ['original_phrase' => $phrase])
                    ];
                }
            }
        }
        
        return null;
    }
    
    /**
     * Get a list of common idioms for a given language
     * 
     * @param string $languageCode Language code
     * @return array List of common idioms
     */
    protected function getCommonIdioms(string $languageCode): array
    {
        // For now, hardcode some common idioms by language
        // In the future, this should be moved to a database
        $commonIdioms = [
            'en' => [
                'break a leg',
                'piece of cake',
                'cost an arm and a leg',
                'hit the nail on the head',
                'once in a blue moon',
                'a penny for your thoughts',
                'beat around the bush',
                'it\'s raining cats and dogs',
                'kill two birds with one stone',
                'the ball is in your court',
                'barking up the wrong tree',
                'bite off more than you can chew',
                'by the skin of your teeth',
                'cut to the chase',
                'get your act together',
                'hang in there',
                'jump on the bandwagon',
                'let the cat out of the bag',
                'miss the boat',
                'pull yourself together',
                'spill the beans',
                'throw in the towel',
                'under the weather',
                'when pigs fly',
                'add insult to injury',
                'back to square one',
                'call it a day',
                'down to earth',
                'easy come easy go',
                'hit the road',
                'in hot water',
                'on thin ice',
                'see eye to eye',
                'speak of the devil',
                'the last straw',
                'the best of both worlds',
                'time flies',
                'off the hook',
                'getting cold feet',
                'break the ice',
                'cut corners',
                'a fish out of water',
                'on cloud nine',
                'cross that bridge when you come to it',
                'out of the blue'
            ],
            'es' => [
                'meter la pata',
                'tomar el pelo',
                'costar un ojo de la cara',
                'dar en el clavo',
                'de vez en cuando',
                'hablar por los codos',
                'estar en las nubes',
                'llueve a cántaros',
                'matar dos pájaros de un tiro',
                'poner las cartas sobre la mesa',
                'estar como una cabra',
                'más vale tarde que nunca'
            ],
            'fr' => [
                'briser la glace',
                'coûter les yeux de la tête',
                'mettre le doigt sur le problème',
                'une fois de temps en temps',
                'avoir un poil dans la main',
                'avoir la tête dans les nuages',
                'il pleut des cordes',
                'faire d\'une pierre deux coups',
                'mettre cartes sur table',
                'être fou comme un lapin de mars',
                'mieux vaut tard que jamais'
            ],
            'de' => [
                'Hals und Beinbruch',
                'ein Kinderspiel',
                'ein Vermögen kosten',
                'den Nagel auf den Kopf treffen',
                'alle Jubeljahre',
                'jemanden ein Loch in den Bauch fragen',
                'auf Wolke sieben schweben',
                'es regnet in Strömen',
                'zwei Fliegen mit einer Klappe schlagen',
                'die Karten auf den Tisch legen',
                'einen Vogel haben',
                'besser spät als nie'
            ],
            'it' => [
                'in bocca al lupo',
                'un gioco da ragazzi',
                'costare un occhio della testa',
                'centrare il punto',
                'ogni morte di papa',
                'parlare a ruota libera',
                'avere la testa fra le nuvole',
                'piove a catinelle',
                'prendere due piccioni con una fava',
                'mettere le carte in tavola',
                'essere fuori come un balcone',
                'meglio tardi che mai'
            ],
            'pt' => [
                'quebrar a perna',
                'moleza',
                'custar os olhos da cara',
                'acertar na mosca',
                'de vez em quando',
                'falar pelos cotovelos',
                'estar com a cabeça nas nuvens',
                'chover canivetes',
                'matar dois coelhos com uma cajadada',
                'pôr as cartas na mesa',
                'ser maluco',
                'antes tarde do que nunca'
            ],
            'ru' => [
                'ни пуха ни пера',
                'проще простого',
                'стоить целое состояние',
                'попасть в точку',
                'раз в сто лет',
                'болтать без умолку',
                'витать в облаках',
                'льёт как из ведра',
                'убить двух зайцев одним выстрелом',
                'положить карты на стол',
                'быть не в своём уме',
                'лучше поздно, чем никогда'
            ],
            'zh' => [
                '祝你成功',
                '小菜一碟',
                '花费很多钱',
                '一语中的',
                '千载难逢',
                '唠叨不停',
                '头脑发热',
                '倾盆大雨',
                '一石二鸟',
                '摊牌',
                '疯了',
                '迟做总比不做好'
            ],
            'ja' => [
                '幸運を祈る',
                '朝飯前',
                '目が飛び出るほど高い',
                '図星',
                'めったにない',
                'おしゃべり',
                '頭が雲の中',
                '土砂降り',
                '一石二鳥',
                '正々堂々と',
                '頭がおかしい',
                '遅くても来ないよりまし'
            ],
        ];
        
        // Return the list for this language, or empty array if not found
        return $commonIdioms[$languageCode] ?? [];
    }

    /**
     * Translate a long text by breaking it into smaller chunks
     * 
     * @param string $text Text to translate
     * @param string $sourceLanguageCode Source language code
     * @param string $targetLanguageCode Target language code
     * @param bool $containsIdioms Whether the text contains idioms
     * @return array Translation result
     */
    protected function translateLongText(string $text, string $sourceLanguageCode, string $targetLanguageCode, bool $containsIdioms): array
    {
        // If the text contains idioms, try to check first if we can identify any
        if ($containsIdioms) {
            $idiomInLongerText = $this->checkForIdiomsInLongerText($text, $sourceLanguageCode, $targetLanguageCode);
            if ($idiomInLongerText !== null) {
                Log::debug('Found idiom in longer text before chunking');
                return $idiomInLongerText;
            }
        }
        
        // Split text into sentences or chunks to preserve context
        $chunks = $this->splitTextIntoChunks($text, $this->maxChunkSize);
        
        Log::debug('Split text into chunks', [
            'chunk_count' => count($chunks)
        ]);
        
        $translatedChunks = [];
        $translationMethod = 'libre_translate';
        $translationFailed = false;
        
        foreach ($chunks as $index => $chunk) {
            try {
                // If chunk has idioms, check for idioms first
                if ($containsIdioms) {
                    $idiomResult = $this->idiomTranslationService->translateIdiom($chunk, $sourceLanguageCode, $targetLanguageCode);
                    if ($idiomResult !== null) {
                        Log::debug('Found idiom in chunk ' . ($index + 1), $idiomResult);
                        $translatedChunks[] = $idiomResult['translation'];
                        if ($translationMethod === 'libre_translate') {
                            $translationMethod = 'mixed_idiom_api';
                        }
                        continue;
                    }
                }
                
                // Use the regular LibreTranslate API for each chunk
                $requestData = [
                    'q' => $chunk,
                    'source' => $sourceLanguageCode,
                    'target' => $targetLanguageCode,
                    'format' => 'text'
                ];
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->timeout(20)->post($this->libreTranslateEndpoint, $requestData);
                
                if ($response->successful()) {
                    $result = $response->json();
                    if (isset($result['translatedText']) && !empty($result['translatedText'])) {
                        $translatedChunks[] = $result['translatedText'];
                        continue;
                    }
                }
                
                // If the primary endpoint fails, try fallback endpoints
                foreach ($this->fallbackEndpoints as $fallbackEndpoint) {
                    $altResponse = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->timeout(20)->post($fallbackEndpoint, $requestData);
                    
                    if ($altResponse->successful()) {
                        $altResult = $altResponse->json();
                        if (isset($altResult['translatedText']) && !empty($altResult['translatedText'])) {
                            $translatedChunks[] = $altResult['translatedText'];
                            $translationMethod = 'libre_translate_fallback';
                            continue 2; // Continue the outer loop
                        }
                    }
                }
                
                // If all else fails, use Google Translate API as a fallback for this chunk
                $translationFailed = true;  // Mark as failed since the primary endpoints didn't work
                $googleResult = $this->translateWithGoogleTranslateAPI($chunk, $sourceLanguageCode, $targetLanguageCode);
                if ($googleResult !== null) {
                    $translatedChunks[] = $googleResult;
                    $translationMethod = 'google_translate_api';
                    continue;
                }
                
                // If all else fails, use our basic dictionary fallback (but likely won't be great for chunks)
                $fallbackResult = $this->getFallbackTranslation($chunk, $sourceLanguageCode, $targetLanguageCode);
                $translatedChunks[] = $fallbackResult;
                $translationMethod = 'fallback_dictionary';
                
            } catch (\Exception $e) {
                Log::error('Error translating chunk ' . ($index + 1), [
                    'exception' => $e->getMessage()
                ]);
                
                // If translation fails for a chunk, keep at least the original text
                $translatedChunks[] = $chunk;
                $translationFailed = true;
            }
        }
        
        // Join the translated chunks
        $translatedText = implode(' ', $translatedChunks);
        
        return [
            'translated_text' => $translatedText,
            'translation_method' => $translationMethod,
            'contains_idioms' => $containsIdioms,
            'is_idiom' => false
        ];
    }
    
    /**
     * Split text into chunks that preserve sentence boundaries when possible
     * 
     * @param string $text Text to split
     * @param int $maxChunkSize Maximum chunk size
     * @return array Array of text chunks
     */
    protected function splitTextIntoChunks(string $text, int $maxChunkSize): array
    {
        // Basic sentence splitting pattern
        $pattern = '/(?<=[.!?])\s+/';
        $sentences = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY);
        
        $chunks = [];
        $currentChunk = '';
        
        foreach ($sentences as $sentence) {
            // If adding this sentence exceeds the limit, start a new chunk
            if (strlen($currentChunk) + strlen($sentence) > $maxChunkSize && !empty($currentChunk)) {
                $chunks[] = $currentChunk;
                $currentChunk = '';
            }
            
            // If a single sentence is longer than max size, split by words
            if (strlen($sentence) > $maxChunkSize) {
                $words = explode(' ', $sentence);
                $wordChunk = '';
                
                foreach ($words as $word) {
                    if (strlen($wordChunk) + strlen($word) + 1 > $maxChunkSize && !empty($wordChunk)) {
                        if (!empty($currentChunk)) {
                            $chunks[] = $currentChunk;
                        }
                        $chunks[] = $wordChunk;
                        $wordChunk = '';
                        $currentChunk = '';
                    }
                    $wordChunk .= (!empty($wordChunk) ? ' ' : '') . $word;
                }
                
                if (!empty($wordChunk)) {
                    $currentChunk = $wordChunk;
                }
            } else {
                $currentChunk .= (!empty($currentChunk) ? ' ' : '') . $sentence;
            }
        }
        
        // Add the last chunk if not empty
        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }
        
        return $chunks;
    }

    /**
     * Try to translate using Google Translate API
     * This serves as an additional fallback when LibreTranslate fails
     * 
     * @param string $text Text to translate
     * @param string $sourceLanguageCode Source language code
     * @param string $targetLanguageCode Target language code
     * @return string|null Translated text or null if failed
     */
    protected function translateWithGoogleTranslateAPI(string $text, string $sourceLanguageCode, string $targetLanguageCode): ?string
    {
        try {
            // Use a public Google Translate API proxy (example)
            $endpoint = 'https://translate.googleapis.com/translate_a/single';
            
            $response = Http::get($endpoint, [
                'client' => 'gtx',
                'sl' => $sourceLanguageCode,
                'tl' => $targetLanguageCode,
                'dt' => 't',
                'q' => $text
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                if (isset($result[0]) && is_array($result[0])) {
                    $translatedParts = [];
                    foreach ($result[0] as $part) {
                        if (isset($part[0])) {
                            $translatedParts[] = $part[0];
                        }
                    }
                    return implode('', $translatedParts);
                }
            }
        } catch (\Exception $e) {
            Log::error('Google Translate API error: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get a fallback translation for common words or phrases.
     * This is used when the translation API fails.
     *
     * @param string $text Text to translate
     * @param string $sourceLanguageCode Source language code
     * @param string $targetLanguageCode Target language code
     * @return string Translated text or original with a note
     */
    protected function getFallbackTranslation(string $text, string $sourceLanguageCode, string $targetLanguageCode): string
    {
        // Basic dictionary of common phrases - expand as needed
        $dictionary = [
            'en' => [
                'hi' => [
                    'hello' => 'नमस्ते',
                    'thank you' => 'धन्यवाद',
                    'yes' => 'हां',
                    'no' => 'नहीं',
                    'please' => 'कृपया',
                    'sorry' => 'माफ़ करें',
                    'good morning' => 'सुप्रभात',
                    'good night' => 'शुभ रात्रि',
                    'how are you' => 'आप कैसे हैं?',
                    'my name is' => 'मेरा नाम है',
                    'what is your name' => 'आपका नाम क्या है?',
                    'i love you' => 'मैं तुमसे प्यार करता हूँ / मैं तुमसे प्यार करती हूँ',
                    'excuse me' => 'क्षमा करें',
                    'welcome' => 'स्वागत है',
                    'goodbye' => 'अलविदा',
                    'water' => 'पानी',
                    'food' => 'भोजन',
                    'break a leg' => 'शुभकामनाएं',
                    'piece of cake' => 'बहुत आसान काम',
                    'cost an arm and a leg' => 'बहुत महंगा होना',
                    'once in a blue moon' => 'कभी-कभार'
                ]
            ],
            'hi' => [
                'en' => [
                    'नमस्ते' => 'Hello',
                    'धन्यवाद' => 'Thank you',
                    'हां' => 'Yes',
                    'नहीं' => 'No',
                    'कृपया' => 'Please',
                    'माफ़ करें' => 'Sorry',
                    'सुप्रभात' => 'Good morning',
                    'शुभ रात्रि' => 'Good night',
                    'आप कैसे हैं?' => 'How are you?',
                    'मेरा नाम है' => 'My name is',
                    'आपका नाम क्या है?' => 'What is your name?',
                    'मैं तुमसे प्यार करता हूँ' => 'I love you (said by male)',
                    'मैं तुमसे प्यार करती हूँ' => 'I love you (said by female)',
                    'क्षमा करें' => 'Excuse me',
                    'स्वागत है' => 'Welcome',
                    'अलविदा' => 'Goodbye',
                    'पानी' => 'Water',
                    'भोजन' => 'Food',
                    'शुभकामनाएं' => 'Good luck / Break a leg',
                    'बहुत आसान काम' => 'Very easy task / Piece of cake',
                    'बहुत महंगा होना' => 'To be very expensive / Cost an arm and a leg'
                ]
            ]
        ];

        // Check if we have this language pair in our dictionary
        if (isset($dictionary[$sourceLanguageCode][$targetLanguageCode])) {
            // First check for exact match with whole text
            $lowerText = strtolower(trim($text));
            if (isset($dictionary[$sourceLanguageCode][$targetLanguageCode][$lowerText])) {
                return $dictionary[$sourceLanguageCode][$targetLanguageCode][$lowerText];
            }
            
            // Check for partial matches in phrases
            foreach ($dictionary[$sourceLanguageCode][$targetLanguageCode] as $source => $target) {
                if (stripos($lowerText, $source) !== false) {
                    return str_ireplace($source, $target, $text);
                }
            }
            
            // Try to translate each word in the sentence
            $words = explode(' ', strtolower(trim($text)));
            $translatedWords = [];
            $anyTranslated = false;
            
            foreach ($words as $word) {
                $cleanWord = trim($word, ".,!?:;-()\"'");
                
                if (isset($dictionary[$sourceLanguageCode][$targetLanguageCode][$cleanWord])) {
                    $translatedWords[] = $dictionary[$sourceLanguageCode][$targetLanguageCode][$cleanWord];
                    $anyTranslated = true;
                } else {
                    $translatedWords[] = $word;
                }
            }
            
            if ($anyTranslated) {
                return implode(' ', $translatedWords) . ' (Partial translation)';
            }
        }

        // If no match is found, return the original text with a note
        return $text . ' (Translation unavailable)';
    }

    /**
     * Aggressive idiom search when user explicitly marks text as containing idioms
     * This method tries various processing techniques to find idioms in text
     * 
     * @param string $text Text to search
     * @param string $sourceLanguageCode Source language code
     * @param string $targetLanguageCode Target language code
     * @return array|null Translation result if idiom found, null otherwise
     */
    protected function aggressiveIdiomSearch(string $text, string $sourceLanguageCode, string $targetLanguageCode): ?array
    {
        // 1. Try with normalized text (lowercase, no punctuation)
        $normalizedText = preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower(trim($text)));
        
        // Get common idioms for this language
        $commonIdioms = $this->getCommonIdioms($sourceLanguageCode);
        
        // 2. Try word pattern matching
        foreach ($commonIdioms as $idiom) {
            $normalizedIdiom = preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower(trim($idiom)));
            
            // Try different matching patterns
            $patterns = [
                $normalizedIdiom,                      // Exact normalized match
                ".*" . preg_quote($normalizedIdiom) . ".*", // Contains idiom
                str_replace(' ', '.+', preg_quote($normalizedIdiom)) // Words in same order but with other words in between
            ];
            
            foreach ($patterns as $pattern) {
                if (preg_match("/$pattern/i", $normalizedText)) {
                    $idiomResult = $this->idiomTranslationService->translateIdiom($idiom, $sourceLanguageCode, $targetLanguageCode);
                    if ($idiomResult !== null) {
                        // Format output based on match type
                        return [
                            'translated_text' => str_ireplace($idiom, $idiomResult['translation'], $text),
                            'translation_method' => 'idiom_aggressive_search',
                            'contains_idioms' => true,
                            'is_idiom' => true,
                            'idiom_data' => array_merge($idiomResult, [
                                'matched_idiom' => $idiom,
                                'match_pattern' => $pattern
                            ])
                        ];
                    }
                }
            }
        }
        
        // 3. Try matching word groups
        $words = preg_split('/\s+/', $normalizedText);
        foreach ($commonIdioms as $idiom) {
            $idiomWords = preg_split('/\s+/', preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower(trim($idiom))));
            
            // Calculate word overlap
            $matchedWords = array_intersect($words, $idiomWords);
            $matchRatio = count($matchedWords) / count($idiomWords);
            
            // If we have a significant word overlap (>70%), consider it a potential match
            if ($matchRatio > 0.7) {
                $idiomResult = $this->idiomTranslationService->translateIdiom($idiom, $sourceLanguageCode, $targetLanguageCode);
                if ($idiomResult !== null) {
                    return [
                        'translated_text' => str_ireplace($idiom, $idiomResult['translation'], $text),
                        'translation_method' => 'idiom_word_overlap',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, [
                            'matched_idiom' => $idiom,
                            'match_ratio' => $matchRatio,
                            'matched_words' => array_values($matchedWords)
                        ])
                    ];
                }
            }
        }
        
        // 4. For English source, check against known idiom patterns
        if ($sourceLanguageCode === 'en') {
            $results = $this->checkEnglishIdiomaticPatterns($text, $targetLanguageCode);
            if ($results !== null) {
                return $results;
            }
        }
        
        return null;
    }
    
    /**
     * Check for English idiomatic patterns that might be variations of known idioms
     * 
     * @param string $text Text to check
     * @param string $targetLanguageCode Target language code
     * @return array|null Translation result if found, null otherwise
     */
    protected function checkEnglishIdiomaticPatterns(string $text, string $targetLanguageCode): ?array
    {
        $patterns = [
            // Various ways people might express idioms
            'break(?:ing)?\s+(?:a|my|your|his|her|their)\s+leg' => 'break a leg',
            '(?:it\'?s|it is)\s+(?:a)?\s+piece\s+of\s+cake' => 'piece of cake',
            'costs?(?:ing)?\s+(?:an|me|him|her|you|them)\s+(?:an)?\s+arm\s+and\s+(?:a)?\s+leg' => 'cost an arm and a leg',
            'hit(?:ting)?\s+the\s+nail\s+on\s+(?:the|its)\s+head' => 'hit the nail on the head',
            'once\s+in\s+a\s+blue\s+moon' => 'once in a blue moon',
            'penny\s+for\s+(?:your|my|his|her|their)\s+thoughts' => 'a penny for your thoughts',
            'beat(?:ing)?\s+around\s+the\s+bush' => 'beat around the bush',
            '(?:it\'?s|it is)\s+raining\s+cats\s+and\s+dogs' => 'it\'s raining cats and dogs',
            'kill(?:ing)?\s+two\s+birds\s+with\s+(?:one|a|the same)\s+stone' => 'kill two birds with one stone',
            'the\s+ball(?:\'?s)?\s+(?:is)?\s+in\s+your\s+court' => 'the ball is in your court',
            'bark(?:ing)?\s+up\s+the\s+wrong\s+tree' => 'barking up the wrong tree',
            'bit(?:e|ing)?\s+off\s+more\s+than\s+(?:I|you|he|she|we|they)\s+can\s+chew' => 'bite off more than you can chew'
        ];
        
        foreach ($patterns as $pattern => $idiom) {
            if (preg_match("/$pattern/i", $text)) {
                $idiomResult = $this->idiomTranslationService->translateIdiom($idiom, 'en', $targetLanguageCode);
                if ($idiomResult !== null) {
                    // Get the matched portion to replace
                    preg_match("/$pattern/i", $text, $matches);
                    $matchedText = $matches[0];
                    
                    return [
                        'translated_text' => str_ireplace($matchedText, $idiomResult['translation'], $text),
                        'translation_method' => 'idiom_pattern_match',
                        'contains_idioms' => true,
                        'is_idiom' => true,
                        'idiom_data' => array_merge($idiomResult, [
                            'matched_idiom' => $idiom,
                            'matched_text' => $matchedText,
                            'pattern' => $pattern
                        ])
                    ];
                }
            }
        }
        
        return null;
    }
} 