<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesFormRequest;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $series = Series::all();
        $mensagemSucesso = $request->session()->get('mensagem.sucesso');

        return view('series.index')
            ->with('series', $series)->with('mensagemSucesso', $mensagemSucesso);
    }

    public function create()
    {
        return view('series.create');
    }

    public function store(SeriesFormRequest $request)
    {
        $serie = Series::create($request->all());

        $seasons = [];
        for ($i = 1; $i <= $request->seasonsQty; $i++) {
            $seasons[] = [
                'series_id' => $serie->id,
                'number' => $i,
            ];
        }
        Season::insert($seasons);

        $episodes = [];
        foreach ($serie->seasons as $season) {
            for ($j = 1; $j <= $request->episodesPerSeason; $j++) {
                $episodes[] = [
                    'season_id' => $season->id,
                    'number' => $i,
                ];
            }
        }
        Episode::insert($episodes);

        return to_route('series.index')->with('mensagem.sucesso', "Série '{$serie->nome}' adicionada com sucesso");
    }

    public function edit(Series $series)
    {
        return view('series.edit')->with('series', $series);
    }

    public function update(SeriesFormRequest $request, Series $series)
    {
        $series->fill($request->all());
        $series->save();
        
        return to_route('series.index')
            ->with('mensagem.sucesso', "Série '{$series->nome}' atualizada com sucesso");
    }

    public function destroy(Series $series)
    {
        $series->delete();
        //Series::destroy($request->series);

        return to_route('series.index')
            ->with('mensagem.sucesso', 'Série removida com sucesso');
    }
}
