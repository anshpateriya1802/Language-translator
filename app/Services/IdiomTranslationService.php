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
                'hi' => 'शुभकामनाएं (अच्छे प्रदर्शन के लिए शुभकामना)',
                'es' => 'Buena suerte (expresión de desear buena suerte)',
                'fr' => 'Bonne chance (souhait de réussite)',
                'de' => 'Hals- und Beinbruch (Wunsch für Erfolg)'
            ],
            'piece of cake' => [
                'hi' => 'बहुत आसान काम (कोई काम जो बहुत आसान है)',
                'es' => 'Pan comido (algo muy fácil de hacer)',
                'fr' => 'C\'est du gâteau (quelque chose de très facile)',
                'de' => 'Ein Kinderspiel (etwas sehr einfaches)'
            ],
            'cost an arm and a leg' => [
                'hi' => 'बहुत महंगा होना (इतना महंगा कि आपको अपने अंग बेचने पड़ें)',
                'es' => 'Costar un ojo de la cara (ser muy caro)',
                'fr' => 'Coûter les yeux de la tête (être très cher)',
                'de' => 'Ein Vermögen kosten (sehr teuer sein)'
            ],
            'hit the nail on the head' => [
                'hi' => 'सही बात कहना (बिल्कुल सही बात कहना या करना)',
                'es' => 'Dar en el clavo (acertar completamente)',
                'fr' => 'Mettre dans le mille (être tout à fait exact)',
                'de' => 'Den Nagel auf den Kopf treffen (genau richtig liegen)'
            ],
            'once in a blue moon' => [
                'hi' => 'कभी-कभार (बहुत कम होने वाली घटना)',
                'es' => 'De higos a brevas (algo que ocurre muy raramente)',
                'fr' => 'Une fois n\'est pas coutume (quelque chose de très rare)',
                'de' => 'Alle Jubeljahre (sehr selten)'
            ],
            'a penny for your thoughts' => [
                'hi' => 'आप क्या सोच रहे हैं? (किसी के विचारों के बारे में पूछना)',
                'es' => '¿En qué piensas? (preguntar por los pensamientos de alguien)',
                'fr' => 'Un sou pour tes pensées (demander à quelqu\'un à quoi il pense)',
                'de' => 'Was geht dir durch den Kopf? (nach jemandes Gedanken fragen)'
            ],
            'beat around the bush' => [
                'hi' => 'बात को घुमाना-फिराना (मुख्य विषय से बचना)',
                'es' => 'Andarse por las ramas (evitar el tema principal)',
                'fr' => 'Tourner autour du pot (éviter le sujet principal)',
                'de' => 'Um den heißen Brei herumreden (das Hauptthema vermeiden)'
            ],
            'it\'s raining cats and dogs' => [
                'hi' => 'मूसलाधार बारिश हो रही है (बहुत तेज़ बारिश)',
                'es' => 'Está lloviendo a cántaros (lluvia muy intensa)',
                'fr' => 'Il pleut des cordes (pluie très forte)',
                'de' => 'Es gießt wie aus Eimern (sehr starker Regen)'
            ],
            'it is raining cats and dogs' => [
                'hi' => 'मूसलाधार बारिश हो रही है (बहुत तेज़ बारिश)',
                'es' => 'Está lloviendo a cántaros (lluvia muy intensa)',
                'fr' => 'Il pleut des cordes (pluie très forte)',
                'de' => 'Es gießt wie aus Eimern (sehr starker Regen)'
            ],
            'kill two birds with one stone' => [
                'hi' => 'एक तीर से दो निशाने (एक कार्रवाई से दो उद्देश्य पूरे करना)',
                'es' => 'Matar dos pájaros de un tiro (lograr dos objetivos con una sola acción)',
                'fr' => 'Faire d\'une pierre deux coups (atteindre deux objectifs avec une seule action)',
                'de' => 'Zwei Fliegen mit einer Klappe schlagen (zwei Ziele mit einer Handlung erreichen)'
            ],
            'the ball is in your court' => [
                'hi' => 'अब आपकी बारी है (अब फैसला या कार्रवाई आपके हाथ में है)',
                'es' => 'La pelota está en tu tejado (es tu turno de decidir o actuar)',
                'fr' => 'La balle est dans ton camp (c\'est à toi de décider ou d\'agir)',
                'de' => 'Der Ball liegt bei dir (es liegt an dir, zu entscheiden oder zu handeln)'
            ]
        ],
        'hi' => [
            // Hindi idioms
            'आँखों का तारा' => [
                'en' => 'Apple of one\'s eye (someone very precious)',
                'es' => 'La niña de los ojos (persona muy preciada)',
                'fr' => 'La prunelle de ses yeux (quelqu\'un de très précieux)',
                'de' => 'Sein Augapfel (jemand, der sehr geschätzt wird)'
            ],
            'नाक में दम करना' => [
                'en' => 'To be a pain in the neck (to annoy someone constantly)',
                'es' => 'Ser un dolor de cabeza (molestar constantemente)',
                'fr' => 'Casser les pieds (embêter constamment)',
                'de' => 'Auf die Nerven gehen (jemanden ständig nerven)'
            ],
            'चार चाँद लगाना' => [
                'en' => 'To add icing on the cake (to make something even better)',
                'es' => 'Ser la guinda del pastel (hacer algo aún mejor)',
                'fr' => 'Être la cerise sur le gâteau (rendre quelque chose encore meilleur)',
                'de' => 'Das i-Tüpfelchen sein (etwas noch besser machen)'
            ],
            'हाथ तंग होना' => [
                'en' => 'To be short of money (to be in financial difficulty)',
                'es' => 'Estar en apuros económicos (tener dificultades financieras)',
                'fr' => 'Être à court d\'argent (avoir des difficultés financières)',
                'de' => 'Knapp bei Kasse sein (in finanziellen Schwierigkeiten sein)'
            ],
            'अपना उल्लू सीधा करना' => [
                'en' => 'To serve one\'s own interest (to act in self-interest)',
                'es' => 'Mirar por sus propios intereses (actuar por interés propio)',
                'fr' => 'Tirer la couverture à soi (agir dans son propre intérêt)',
                'de' => 'Seinen eigenen Vorteil suchen (im Eigeninteresse handeln)'
            ]
        ],
        'es' => [
            // Spanish idioms
            'estar en las nubes' => [
                'en' => 'To have your head in the clouds (to be distracted or not paying attention)',
                'hi' => 'ख्यालों में खोए होना (ध्यान नहीं देना)',
                'fr' => 'Être dans les nuages (être distrait ou ne pas faire attention)',
                'de' => 'Mit dem Kopf in den Wolken sein (abgelenkt sein oder nicht aufpassen)'
            ]
        ],
        'fr' => [
            // French idioms
            'avoir un poil dans la main' => [
                'en' => 'To be very lazy (literally: to have a hair in the hand)',
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
            'target' => $targetLanguageCode
        ]);
        
        // Normalize the text (lowercase, trim, remove punctuation)
        $normalizedText = $this->normalizeText($text);
        
        Log::debug('Normalized text for idiom check', ['original' => $text, 'normalized' => $normalizedText]);
        
        // Check if the source language has idioms in our database
        if (isset($this->idiomDatabase[$sourceLanguageCode])) {
            $idioms = $this->idiomDatabase[$sourceLanguageCode];
            
            // Look for exact matches first
            foreach ($idioms as $idiom => $translations) {
                $normalizedIdiom = $this->normalizeText($idiom);
                
                // Check for direct match
                if ($normalizedText === $normalizedIdiom && isset($translations[$targetLanguageCode])) {
                    Log::debug('Found exact idiom match', ['idiom' => $idiom]);
                    return ['translation' => $translations[$targetLanguageCode], 'is_idiom' => true];
                }
                
                // Check for match with question mark
                if ($normalizedText === $normalizedIdiom . '?' && isset($translations[$targetLanguageCode])) {
                    Log::debug('Found exact idiom match with question mark', ['idiom' => $idiom]);
                    return ['translation' => $translations[$targetLanguageCode], 'is_idiom' => true];
                }
            }
            
            // Look for idioms that might be part of a longer text
            foreach ($idioms as $idiom => $translations) {
                $normalizedIdiom = $this->normalizeText($idiom);
                
                if (stripos($normalizedText, $normalizedIdiom) !== false && isset($translations[$targetLanguageCode])) {
                    Log::debug('Found partial idiom match', ['idiom' => $idiom, 'within' => $normalizedText]);
                    return ['translation' => $translations[$targetLanguageCode], 'is_idiom' => true];
                }
            }
            
            // The text itself might be part of a larger idiom
            foreach ($idioms as $idiom => $translations) {
                $normalizedIdiom = $this->normalizeText($idiom);
                
                // If text is at least 4 characters and is a substring of an idiom (for partial matches)
                if (strlen($normalizedText) >= 4 && 
                    stripos($normalizedIdiom, $normalizedText) !== false && 
                    isset($translations[$targetLanguageCode])) {
                    Log::debug('Text is part of a known idiom', ['text' => $normalizedText, 'idiom' => $idiom]);
                    return ['translation' => $translations[$targetLanguageCode], 'is_idiom' => true, 'full_idiom' => $idiom];
                }
            }
        }
        
        // Check common variations
        if ($sourceLanguageCode === 'en') {
            // Check common variations for English idioms
            $result = $this->checkEnglishVariations($normalizedText, $targetLanguageCode);
            if ($result !== null) {
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
            'raining cats and dogs' => ['its raining cats and dogs', 'it is raining cats and dogs'],
            'piece of cake' => ['thats a piece of cake', 'that is a piece of cake', 'its a piece of cake'],
            'cost an arm and a leg' => ['costs an arm and a leg', 'costing an arm and a leg'],
            'beat around the bush' => ['beating around the bush', 'beats around the bush'],
        ];
        
        foreach ($variations as $baseIdiom => $variants) {
            if (in_array($normalizedText, $variants) && 
                isset($this->idiomDatabase['en'][$baseIdiom]) && 
                isset($this->idiomDatabase['en'][$baseIdiom][$targetLanguageCode])) {
                Log::debug('Found idiom through variation', ['variation' => $normalizedText, 'base' => $baseIdiom]);
                return [
                    'translation' => $this->idiomDatabase['en'][$baseIdiom][$targetLanguageCode], 
                    'is_idiom' => true
                ];
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