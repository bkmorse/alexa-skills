<?php

namespace App\Http\Controllers;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class RainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function rain()
    {
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => env('DARK_SKY_API_URL')]);
            $request = $client->request('GET', env('ADDRESS_LAT_LONG'));

        } catch (RequestException $e) {
            $status = $e->hasResponse() ? Psr7\str($e->getResponse()) : 'There was an issue getting the info from FormStack';
            var_dump($status);
        } catch (\Exception $e) {
            $status = $e->hasResponse() ? Psr7\str($e->getResponse()) : 'There was an issue getting the info from FormStack';
            var_dump($status);
        }

        $response = json_decode($request->getBody());

        $when = false;
        $minutes = $response->minutely->data;

        for ($i=0; $i < count($minutes); $i++) { 

            // if (isset($minutes[$i]->summary)) print $minutes[$i]->summary . '</br>';

            // if ($minutes[$i]->precipProbability >= 0.40) {
            if (isset($minutes[$i]->summary)) {

                $when = date('g:ia', $minutes[$i]->time);
                $percentage = $minutes[$i]->precipProbability*100;
                $intensity = $minutes[$i]->summary;
                // $intensity = $this->getIntensity($minutes[$i]->precipIntensity);
                break;
            }
        }

        if ($when) {
            print 'There is a ' . $percentage . ' percent chance of ' . $intensity . ' at' . $when;
        } else {
            print 'it will not rain in the next hour';
        }

    }

    public function getIntensity($intensity)
    {
        if ($intensity >= .7) return 'heavy rain';
        if ($intensity >= .5) return 'medium rain';
        if ($intensity >= .2) return 'light rain';
        if ($intensity >= .0) return 'drizzle';
    }
}
