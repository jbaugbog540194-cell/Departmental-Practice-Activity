<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlantController extends Controller
{
    public function index(): View
    {
        $plants = Plant::query()->with('user')->latest()->paginate(10);

        return view('plants.index', compact('plants'));
    }

    public function create(): View
    {
        return view('plants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPlant($request);

        // The creator is always the authenticated user, never a submitted form value.
        $request->user()->plants()->create($validated);

        return redirect()->route('plants.index')->with('status', 'Plant created successfully.');
    }

    public function show(Plant $plant): View
    {
        return view('plants.show', compact('plant'));
    }

    public function edit(Plant $plant): View
    {
        return view('plants.edit', compact('plant'));
    }

    public function update(Request $request, Plant $plant): RedirectResponse
    {
        $plant->update($this->validatedPlant($request));

        return redirect()->route('plants.show', $plant)->with('status', 'Plant updated successfully.');
    }

    public function destroy(Plant $plant): RedirectResponse
    {
        $plant->delete();

        return redirect()->route('plants.index')->with('status', 'Plant deleted successfully.');
    }

    private function validatedPlant(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['required', 'string', 'max:10000'],
            'stock_status' => ['required', 'in:in_stock,out_of_stock'],
        ]);
    }
}
