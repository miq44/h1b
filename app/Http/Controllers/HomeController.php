<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getHomePage(){
//        $data = $this->homologCompariosnService->getAvailableOrganismName();
        $data = $this->getData();
        return view('layout.home',compact('data'));
    }

    public function getData(){
        $str = file_get_contents(asset('data2.json'));
        $str = trim(preg_replace('/\s\s+/', ' ', $str));
       // $str = json_encode($str);
        return $str;
    }

    public function getDataForBubbleChart(){
        $str = file_get_contents(asset('All_year_data.json'));
        $str = trim(preg_replace('/\s\s+/', ' ', $str));

        $living_cost_index = file_get_contents(asset('city_data_with_sorted_index.json'));
        $living_cost_index = trim(preg_replace('/\s\s+/', ' ', $living_cost_index));
        $data = [];
        $data['living_cost'] = $living_cost_index;
        $data['h1b'] = $str;
        // $str = json_encode($str);;
        return $data;
    }

    public function getBubbleChart(){
        $data = $this->getDataForBubbleChart();
        return view('layout.bubble',compact('data'));
    }
}
