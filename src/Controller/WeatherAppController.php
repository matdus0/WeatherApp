<?php

namespace App\Controller;

use App\Form\GetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherAppController extends AbstractController
{
    #[Route('/weather/app', name: 'app_weather_app')]
    public function index(Request $request): Response
    {


        $form = $this->createForm(GetType::class, [
            'action' => $this->generateUrl('app_weather_app'),
            'method' => 'GET'
        ]);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid())
        {
            /*
             * Niewiadomo dlaczego nie działa
             */
           //$value = $request->request->get('Find_City');

            /*
             *  Działający sposob na wyciagniecie danych metodą GET
             */
           $value = $form->get("Find_City")->getData();
        }

        if(isset($value))
        {
            $_GET['q'] = $value;
        }
        else{
            $_GET['q'] = "London";
        }

        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $_GET['q'] . '&units=metric&appid=4de47654877cb5fb78992988b41742b2';
        $obj = json_decode(file_get_contents($url), true);

        if($obj['cod'] == 200){
            return $this->render('weather_app/index.html.twig', [
                'controller_name' => 'WeatherAppController',
                'clouds' => $obj['weather'][0]['main'],
                'temp' => $obj['main']['temp'],
                'feelsLike' => $obj['main']['feels_like'],
                'pressure' => $obj['main']['pressure'],
                'sunrise' => $obj['sys']['sunrise'],
                'sunset' => $obj['sys']['sunset'],
                'city' => $obj['name'],
                'getForm' => $form->createView()
            ]);
        }
        else{
            return $this->render('weather_app/error.html.twig');
        }


    }
}
