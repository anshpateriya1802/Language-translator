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
     * Idiom Translation Service instance
     *
     * @var IdiomTranslationService
     */
    protected $idiomTranslationService;

    /**
     * Create a new TranslationService instance.
     *
     * @param IdiomTranslationService $idiomTranslationService
     */
    public function __construct(IdiomTranslationService $idiomTranslationService = null)
    {
        // Initialize LibreTranslate endpoint
        $this->libreTranslateEndpoint = config('services.libre_translate.endpoint');
        
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
        
        $isIdiom = false;
        $idiomData = null;
        $translationMethod = 'libre_translate';

        // Try idiom translation first if the text might contain idioms or is short
        // Always check for idioms in short texts - they are more likely to be idioms
        $isShortText = strlen($text) < 100;
        if ($containsIdioms || $isShortText) {
            Log::debug('Checking for idioms', ['text_length' => strlen($text), 'user_marked_idiom' => $containsIdioms]);
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
                Log::debug('No idiom found in text');
            }
        }

        // If not an idiom or idiom translation failed, try LibreTranslate API
        if ($this->libreTranslateEndpoint) {
            try {
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
                ])->post($this->libreTranslateEndpoint, $requestData);
                
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
                    if ($response->status() == 403 || $response->status() == 401) {
                        Log::info('Attempting to use alternative LibreTranslate public endpoint');
                        // Try a different public LibreTranslate instance
                        $alternativeEndpoint = 'https://translate.argosopentech.com/translate';
                        $altResponse = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ])->post($alternativeEndpoint, $requestData);
                        
                        if ($altResponse->successful()) {
                            $altResult = $altResponse->json();
                            if (isset($altResult['translatedText']) && !empty($altResult['translatedText'])) {
                                return [
                                    'translated_text' => $altResult['translatedText'],
                                    'translation_method' => 'libre_translate_alternative',
                                    'contains_idioms' => $containsIdioms,
                                    'is_idiom' => false
                                ];
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
            
            // Check for direct match with whole text
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
        }

        // If no match is found, return the original text with a note
        return $text . ' (Translation unavailable)';
    }
} 