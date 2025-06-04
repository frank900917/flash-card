<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    
    public function status()
    {
        return response()->json([
            'enabled' => $this->isTTSEnabled(),
        ]);
    }

    public function listLanguages()
    {
        if (!$this->isTTSEnabled()) {
            return response()->json(['message' => 'Google TTS is disabled.'], 403);
        }

        $client = new TextToSpeechClient();
        $voices = $client->listVoices(new ListVoicesRequest());
        $languages = [];

        foreach ($voices->getVoices() as $voice) {
            foreach ($voice->getLanguageCodes() as $lang) {
                $languages[$lang] = true;
            }
        }

        $client->close();

        return response()->json(array_keys($languages));
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
        $audioConfig = (new AudioConfig())->setAudioEncoding(AudioEncoding::MP3);
        $request = (new SynthesizeSpeechRequest())
            ->setInput($input)
            ->setVoice($voice)
            ->setAudioConfig($audioConfig);

        $resp = $textToSpeechClient->synthesizeSpeech($request);
        $audioContent = $resp->getAudioContent();

        return response($audioContent)->header('Content-Type', 'audio/mpeg');
    }
}
