<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class IdiomTranslationService
{
    /**
     * Database of idioms with their meanings in different languages
     * Format: [source_language_code => [idiom => [target_language_code => translation]]]
     */
    protected $idiomDatabase = [
        'en' => [
            // English idioms
            'break a leg' => [
                'en' => 'Good luck (a theatrical expression wishing performers good luck before a show)',
                'hi' => 'शुभकामनाएं (अच्छे प्रदर्शन के लिए शुभकामना)',
                'es' => 'Buena suerte (expresión de desear buena suerte)',
                'fr' => 'Bonne chance (souhait de réussite)',
                'de' => 'Hals- und Beinbruch (Wunsch für Erfolg)'
            ],
            'piece of cake' => [
                'en' => 'Very easy task (something that is extremely easy to do)',
                'hi' => 'बहुत आसान काम (कोई काम जो बहुत आसान है)',
                'es' => 'Pan comido (algo muy fácil de hacer)',
                'fr' => 'C\'est du gâteau (quelque chose de très facile)',
                'de' => 'Ein Kinderspiel (etwas sehr einfaches)'
            ],
            'cost an arm and a leg' => [
                'en' => 'Very expensive (something that costs a lot of money)',
                'hi' => 'बहुत महंगा होना (इतना महंगा कि आपको अपने अंग बेचने पड़ें)',
                'es' => 'Costar un ojo de la cara (ser muy caro)',
                'fr' => 'Coûter les yeux de la tête (être très cher)',
                'de' => 'Ein Vermögen kosten (sehr teuer sein)'
            ],
            'hit the nail on the head' => [
                'en' => 'To be exactly right (to identify the exact point)',
                'hi' => 'सही बात कहना (बिल्कुल सही बात कहना या करना)',
                'es' => 'Dar en el clavo (acertar completamente)',
                'fr' => 'Mettre dans le mille (être tout à fait exact)',
                'de' => 'Den Nagel auf den Kopf treffen (genau richtig liegen)'
            ],
            'once in a blue moon' => [
                'en' => 'Very rarely (something that happens extremely infrequently)',
                'hi' => 'कभी-कभार (बहुत कम होने वाली घटना)',
                'es' => 'De higos a brevas (algo que ocurre muy raramente)',
                'fr' => 'Une fois n\'est pas coutume (quelque chose de très rare)',
                'de' => 'Alle Jubeljahre (sehr selten)'
            ],
            'a penny for your thoughts' => [
                'en' => 'Tell me what you\'re thinking (an expression used to ask what someone is thinking about)',
                'hi' => 'आप क्या सोच रहे हैं? (किसी के विचारों के बारे में पूछना)',
                'es' => '¿En qué piensas? (preguntar por los pensamientos de alguien)',
                'fr' => 'Un sou pour tes pensées (demander à quelqu\'un à quoi il pense)',
                'de' => 'Was geht dir durch den Kopf? (nach jemandes Gedanken fragen)'
            ],
            'beat around the bush' => [
                'en' => 'Avoid talking about what\'s important (to discuss a matter without getting to the point)',
                'hi' => 'बात को घुमाना-फिराना (मुख्य विषय से बचना)',
                'es' => 'Andarse por las ramas (evitar el tema principal)',
                'fr' => 'Tourner autour du pot (éviter le sujet principal)',
                'de' => 'Um den heißen Brei herumreden (das Hauptthema vermeiden)'
            ],
            'it\'s raining cats and dogs' => [
                'en' => 'It\'s raining very heavily (a heavy downpour)',
                'hi' => 'मूसलाधार बारिश हो रही है (बहुत तेज़ बारिश)',
                'es' => 'Está lloviendo a cántaros (lluvia muy intensa)',
                'fr' => 'Il pleut des cordes (pluie très forte)',
                'de' => 'Es gießt wie aus Eimern (sehr starker Regen)'
            ],
            'it is raining cats and dogs' => [
                'en' => 'It\'s raining very heavily (a heavy downpour)',
                'hi' => 'मूसलाधार बारिश हो रही है (बहुत तेज़ बारिश)',
                'es' => 'Está lloviendo a cántaros (lluvia muy intensa)',
                'fr' => 'Il pleut des cordes (pluie très forte)',
                'de' => 'Es gießt wie aus Eimern (sehr starker Regen)'
            ],
            'kill two birds with one stone' => [
                'en' => 'Accomplish two things with a single action (achieve two goals with one effort)',
                'hi' => 'एक तीर से दो निशाने (एक कार्रवाई से दो उद्देश्य पूरे करना)',
                'es' => 'Matar dos pájaros de un tiro (lograr dos objetivos con una sola acción)',
                'fr' => 'Faire d\'une pierre deux coups (atteindre deux objectifs avec une seule action)',
                'de' => 'Zwei Fliegen mit einer Klappe schlagen (zwei Ziele mit einer Handlung erreichen)'
            ],
            'the ball is in your court' => [
                'en' => 'It\'s your turn to make a decision or take action (the responsibility is now yours)',
                'hi' => 'अब आपकी बारी है (अब फैसला या कार्रवाई आपके हाथ में है)',
                'es' => 'La pelota está en tu tejado (es tu turno de decidir o actuar)',
                'fr' => 'La balle est dans ton camp (c\'est à toi de décider ou d\'agir)',
                'de' => 'Der Ball liegt bei dir (es liegt an dir, zu entscheiden oder zu handeln)'
            ],
            'barking up the wrong tree' => [
                'en' => 'Looking in the wrong place or accusing the wrong person (pursuing a mistaken line of thought or action)',
                'hi' => 'गलत जगह खोजना (किसी गलत व्यक्ति पर शक करना या गलत दिशा में जांच करना)',
                'es' => 'Estar equivocado (buscar en el lugar incorrecto)',
                'fr' => 'Faire fausse route (se tromper de direction)',
                'de' => 'Auf dem Holzweg sein (in die falsche Richtung gehen)'
            ],
            'bite off more than you can chew' => [
                'en' => 'Take on more responsibility than you can handle (attempt something too ambitious)',
                'hi' => 'अपनी क्षमता से अधिक काम लेना (अपनी क्षमता से अधिक जिम्मेदारी लेना)',
                'es' => 'Abarcar más de lo que se puede apretar (intentar algo demasiado ambicioso)',
                'fr' => 'Voir trop grand (entreprendre plus que ce qu\'on peut accomplir)',
                'de' => 'Sich zu viel vornehmen (mehr Verantwortung übernehmen als man bewältigen kann)'
            ]
        ],
        'hi' => [
            // Hindi idioms
            'आँखों का तारा' => [
                'en' => 'Apple of one\'s eye (someone very precious)',
                'hi' => 'बहुत प्रिय व्यक्ति (कोई ऐसा व्यक्ति जो बहुत प्यारा है)',
                'es' => 'La niña de los ojos (persona muy preciada)',
                'fr' => 'La prunelle de ses yeux (quelqu\'un de très précieux)',
                'de' => 'Sein Augapfel (jemand, der sehr geschätzt wird)'
            ],
            'नाक में दम करना' => [
                'en' => 'To be a pain in the neck (to annoy someone constantly)',
                'hi' => 'बहुत परेशान करना (किसी को लगातार तंग करना)',
                'es' => 'Ser un dolor de cabeza (molestar constantemente)',
                'fr' => 'Casser les pieds (embêter constamment)',
                'de' => 'Auf die Nerven gehen (jemanden ständig nerven)'
            ],
            'चार चाँद लगाना' => [
                'en' => 'To add icing on the cake (to make something even better)',
                'hi' => 'शोभा बढ़ाना (किसी चीज़ को और भी बेहतर बनाना)',
                'es' => 'Ser la guinda del pastel (hacer algo aún mejor)',
                'fr' => 'Être la cerise sur le gâteau (rendre quelque chose encore meilleur)',
                'de' => 'Das i-Tüpfelchen sein (etwas noch besser machen)'
            ],
            'हाथ तंग होना' => [
                'en' => 'To be short of money (to be in financial difficulty)',
                'hi' => 'आर्थिक तंगी होना (वित्तीय कठिनाई में होना)',
                'es' => 'Estar en apuros económicos (tener dificultades financieras)',
                'fr' => 'Être à court d\'argent (avoir des difficultés financières)',
                'de' => 'Knapp bei Kasse sein (in finanziellen Schwierigkeiten sein)'
            ],
            'अपना उल्लू सीधा करना' => [
                'en' => 'To serve one\'s own interest (to act in self-interest)',
                'hi' => 'अपना स्वार्थ साधना (स्वयं के हित में कार्य करना)',
                'es' => 'Mirar por sus propios intereses (actuar por interés propio)',
                'fr' => 'Tirer la couverture à soi (agir dans son propre intérêt)',
                'de' => 'Seinen eigenen Vorteil suchen (im Eigeninteresse handeln)'
            ]
        ],
        'es' => [
            // Spanish idioms
            'estar en las nubes' => [
                'en' => 'To have your head in the clouds (to be distracted or not paying attention)',
                'es' => 'Estar distraído o no prestar atención (estar pensando en otra cosa)',
                'hi' => 'ख्यालों में खोए होना (ध्यान नहीं देना)',
                'fr' => 'Être dans les nuages (être distrait ou ne pas faire attention)',
                'de' => 'Mit dem Kopf in den Wolken sein (abgelenkt sein oder nicht aufpassen)'
            ]
        ],
        'fr' => [
            // French idioms
            'avoir un poil dans la main' => [
                'en' => 'To be very lazy (literally: to have a hair in the hand)',
                'fr' => 'Être très paresseux (ne pas aimer travailler)',
                'hi' => 'बहुत आलसी होना',
                'es' => 'Ser muy perezoso',
                'de' => 'Sehr faul sein'
            ]
        ]
    ];
    
    /**
     * Check if a text is an idiom and translate it to the target language.
     *
     * @param string $text Text to check and translate
     * @param string $sourceLanguageCode Source language code
     * @param string $targetLanguageCode Target language code
     * @return array|null Returns [translation, explanation] if it's an idiom, null otherwise
     */
    public function translateIdiom(string $text, string $sourceLanguageCode, string $targetLanguageCode): ?array
    {
        // Debug information
        Log::debug('Attempting idiom translation', [
            'text' => $text,
            'source' => $sourceLanguageCode,
            'target' => $targetLanguageCode,
            'is_same_language' => ($sourceLanguageCode === $targetLanguageCode)
        ]);
        
        // Normalize the text (lowercase, trim, remove punctuation)
        $normalizedText = $this->normalizeText($text);
        
        Log::debug('Normalized text for idiom check', ['original' => $text, 'normalized' => $normalizedText]);
        
        // Check if the source language has idioms in our database
        if (isset($this->idiomDatabase[$sourceLanguageCode])) {
            $idioms = $this->idiomDatabase[$sourceLanguageCode];
            
            // List to track all potential matches with scoring
            $matches = [];
            
            // Phase 1: Check for exact matches (highest priority)
            foreach ($idioms as $idiom => $translations) {
                $normalizedIdiom = $this->normalizeText($idiom);
                
                // Check for direct match
                if ($normalizedText === $normalizedIdiom || 
                    $normalizedText === $normalizedIdiom . '?' || 
                    $normalizedText === 'the ' . $normalizedIdiom) {
                    
                    if (isset($translations[$targetLanguageCode])) {
                        $matches[] = [
                            'idiom' => $idiom,
                            'translation' => $translations[$targetLanguageCode],
                            'score' => 100, // Highest score for exact matches
                            'match_type' => 'exact'
                        ];
                    }
                }
            }
            
            // If we have exact matches, use the first one (they all have same score)
            if (!empty($matches)) {
                $bestMatch = $matches[0];
                Log::debug('Found exact idiom match', ['idiom' => $bestMatch['idiom']]);
                return [
                    'translation' => $bestMatch['translation'],
                    'is_idiom' => true,
                    'match_type' => 'exact',
                    'original_idiom' => $bestMatch['idiom']
                ];
            }
            
            // Phase 2: Check for idioms as complete phrases within text
            foreach ($idioms as $idiom => $translations) {
                $normalizedIdiom = $this->normalizeText($idiom);
                
                // Skip very short idioms to avoid false matches
                if (strlen($normalizedIdiom) < 8) {
                    continue;
                }
                
                // Check if the idiom appears as a complete phrase
                if (stripos($normalizedText, $normalizedIdiom) !== false &&
                    isset($translations[$targetLanguageCode])) {
                    
                    // Higher score for longer matches
                    $score = 50 + min(40, strlen($normalizedIdiom));
                    
                    // Boost score for word boundary matches
                    $wordBoundaryPattern = '/\b' . preg_quote($normalizedIdiom, '/') . '\b/i';
                    if (preg_match($wordBoundaryPattern, $normalizedText)) {
                        $score += 5;
                    }
                    
                    $matches[] = [
                        'idiom' => $idiom,
                        'translation' => $translations[$targetLanguageCode],
                        'score' => $score,
                        'match_type' => 'contained'
                    ];
                }
            }
            
            // Phase 3: Check for variations if we're dealing with English
            if ($sourceLanguageCode === 'en') {
                $variationMatch = $this->checkEnglishVariations($normalizedText, $targetLanguageCode);
                if ($variationMatch !== null) {
                    $matches[] = [
                        'idiom' => $variationMatch['original_idiom'] ?? $variationMatch['matched_variation'],
                        'translation' => $variationMatch['translation'],
                        'score' => 70, // High score for variation matches
                        'match_type' => 'variation',
                        'variation_data' => $variationMatch
                    ];
                }
            }
            
            // Sort matches by score (highest first)
            if (!empty($matches)) {
                usort($matches, function($a, $b) {
                    return $b['score'] - $a['score'];
                });
                
                $bestMatch = $matches[0];
                Log::debug('Found idiom match', [
                    'idiom' => $bestMatch['idiom'], 
                    'match_type' => $bestMatch['match_type'], 
                    'score' => $bestMatch['score']
                ]);
                
                $result = [
                    'translation' => $bestMatch['translation'],
                    'is_idiom' => true,
                    'match_type' => $bestMatch['match_type'],
                    'original_idiom' => $bestMatch['idiom'],
                    'score' => $bestMatch['score']
                ];
                
                // Include any additional data from variation matches
                if ($bestMatch['match_type'] === 'variation' && isset($bestMatch['variation_data'])) {
                    $result = array_merge($result, $bestMatch['variation_data']);
                }
                
                return $result;
            }
        }
        
        Log::debug('No idiom match found');
        
        // No idiom found
        return null;
    }
    
    /**
     * Normalize text for better idiom matching
     *
     * @param string $text
     * @return string
     */
    protected function normalizeText(string $text): string
    {
        $normalized = strtolower(trim($text));
        
        // Remove apostrophes and replace with spaces
        $normalized = str_replace(["'", "'"], ["", ""], $normalized);
        
        // Remove common punctuation that might affect matching
        $normalized = str_replace(['.', ',', '!', '?', ';', ':'], ['', '', '', '', '', ''], $normalized);
        
        // Remove extra spaces
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        return trim($normalized);
    }
    
    /**
     * Check for common English idiom variations
     *
     * @param string $normalizedText
     * @param string $targetLanguageCode
     * @return array|null
     */
    protected function checkEnglishVariations(string $normalizedText, string $targetLanguageCode): ?array
    {
        // Common variations for English idioms
        $variations = [
            'raining cats and dogs' => [
                'its raining cats and dogs',
                'it is raining cats and dogs',
                'raining cats and dogs',
                'raining like cats and dogs'
            ],
            'piece of cake' => [
                'thats a piece of cake',
                'that is a piece of cake',
                'its a piece of cake',
                'this is a piece of cake',
                'easy as a piece of cake'
            ],
            'cost an arm and a leg' => [
                'costs an arm and a leg',
                'costing an arm and a leg',
                'that costs an arm and a leg',
                'will cost an arm and a leg',
                'it costs an arm and a leg'
            ],
            'beat around the bush' => [
                'beating around the bush',
                'beats around the bush',
                'dont beat around the bush',
                'stop beating around the bush'
            ],
            'break a leg' => [
                'breaking a leg',
                'breaks a leg',
                'i hope you break a leg',
                'break a leg out there'
            ],
            'hit the nail on the head' => [
                'hits the nail on the head',
                'hitting the nail on the head',
                'you hit the nail on the head',
                'that hits the nail on the head'
            ],
            'barking up the wrong tree' => [
                'barks up the wrong tree',
                'youre barking up the wrong tree',
                'you are barking up the wrong tree',
                'i think youre barking up the wrong tree'
            ],
            'the ball is in your court' => [
                'balls in your court',
                'the balls in your court',
                'the ball is now in your court'
            ],
            'kill two birds with one stone' => [
                'killing two birds with one stone',
                'kills two birds with one stone',
                'trying to kill two birds with one stone',
                'like killing two birds with one stone'
            ],
            'once in a blue moon' => [
                'only once in a blue moon',
                'happens once in a blue moon',
                'its once in a blue moon',
                'it is once in a blue moon'
            ]
        ];
        
        foreach ($variations as $baseIdiom => $variants) {
            // First check for exact matches with variants
            foreach ($variants as $variant) {
                if ($normalizedText === $variant) {
                    if (isset($this->idiomDatabase['en'][$baseIdiom]) && 
                        isset($this->idiomDatabase['en'][$baseIdiom][$targetLanguageCode])) {
                        
                        Log::debug('Found exact idiom match through variation', [
                            'variation' => $variant, 
                            'base' => $baseIdiom
                        ]);
                        
                        return [
                            'translation' => $this->idiomDatabase['en'][$baseIdiom][$targetLanguageCode], 
                            'is_idiom' => true,
                            'match_type' => 'variation',
                            'original_idiom' => $baseIdiom,
                            'matched_variation' => $variant
                        ];
                    }
                }
            }
            
            // Then check for idioms contained within text
            foreach ($variants as $variant) {
                if (stripos($normalizedText, $variant) !== false) {
                    if (isset($this->idiomDatabase['en'][$baseIdiom]) && 
                        isset($this->idiomDatabase['en'][$baseIdiom][$targetLanguageCode])) {
                        
                        Log::debug('Found idiom through variation as part of text', [
                            'variation' => $variant, 
                            'base' => $baseIdiom,
                            'in_text' => $normalizedText
                        ]);
                        
                        return [
                            'translation' => $this->idiomDatabase['en'][$baseIdiom][$targetLanguageCode], 
                            'is_idiom' => true,
                            'match_type' => 'variation_contained',
                            'original_idiom' => $baseIdiom,
                            'matched_variation' => $variant
                        ];
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Add a new idiom to the database.
     *
     * @param string $idiom The idiom text
     * @param string $sourceLanguageCode Source language code
     * @param string $targetLanguageCode Target language code
     * @param string $translation Translation in target language
     * @return void
     */
    public function addIdiom(string $idiom, string $sourceLanguageCode, string $targetLanguageCode, string $translation): void
    {
        // Initialize source language if it doesn't exist
        if (!isset($this->idiomDatabase[$sourceLanguageCode])) {
            $this->idiomDatabase[$sourceLanguageCode] = [];
        }
        
        // Initialize idiom if it doesn't exist
        if (!isset($this->idiomDatabase[$sourceLanguageCode][$idiom])) {
            $this->idiomDatabase[$sourceLanguageCode][$idiom] = [];
        }
        
        // Add translation
        $this->idiomDatabase[$sourceLanguageCode][$idiom][$targetLanguageCode] = $translation;
    }
} 