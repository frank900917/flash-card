<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\ListVoicesRequest;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

class GoogleTTSController extends Controller
{
    private function isTTSEnabled()
    {
        return config('services.google_tts.enabled');
    }
    
    // 獲取 Google TTS 狀態
    public function status()
    {
        return response()->json([
            'enabled' => $this->isTTSEnabled(),
        ]);
    }

    // 獲取 Google TTS 支援語言列表
    public function listLanguages()
    {
        if (!$this->isTTSEnabled()) {
            return response()->json(['message' => 'Google TTS is disabled.'], 403);
        }

        // 嘗試從快取取得語言列表
        $languages = Cache::get('google_tts_languages');

        if (!$languages) {
            $client = new TextToSpeechClient();
            $voices = $client->listVoices(new ListVoicesRequest());

            $languageSet = [];

            foreach ($voices->getVoices() as $voice) {
                foreach ($voice->getLanguageCodes() as $lang) {
                    $languageSet[$lang] = true;
                }
            }

            $languages = array_keys($languageSet);

            // 將結果快取 6 小時
            Cache::put('google_tts_languages', $languages, now()->addHours(6));
        }

        return response()->json($languages);
    }

    public function synthesize(Request $request)
    {
        if (!$this->isTTSEnabled()) {
            return response()->json(['message' => 'Google TTS is disabled.'], 403);
        }

        $request->validate([
            'text' => 'required|string|max:500',
            'lang' => 'required|string',
            'speed' => 'nullable|numeric|min:0.25|max:4.0',
        ]);

        $text = $request->input('text');
        $languageCode = $request->input('lang');
        $speed = $request->input('speed', 1.0);
        $textToSpeechClient = new TextToSpeechClient();
        $input = (new SynthesisInput())->setText($text);
        $voice = (new VoiceSelectionParams())->setLanguageCode($languageCode);
        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3)
            ->setSpeakingRate($speed);
        $request = (new SynthesizeSpeechRequest())
            ->setInput($input)
            ->setVoice($voice)
            ->setAudioConfig($audioConfig);

        $resp = $textToSpeechClient->synthesizeSpeech($request);
        $audioContent = $resp->getAudioContent();

        return response($audioContent)->header('Content-Type', 'audio/mpeg');
    }
}
