@csrf

<label for="name">Plant name</label>
<input id="name" name="name" value="{{ old('name', $plant->name ?? '') }}" maxlength="255" required>
@error('name') <p class="error">{{ $message }}</p> @enderror

<label for="category">Category</label>
<input id="category" name="category" list="plant-categories" value="{{ old('category', $plant->category ?? '') }}" maxlength="100" required>
<datalist id="plant-categories">
    <option value="Ornamental"><option value="Flowering"><option value="Succulent"><option value="Herb">
</datalist>
@error('category') <p class="error">{{ $message }}</p> @enderror

<label for="unit_price">Unit price</label>
<input id="unit_price" name="unit_price" type="number" step="0.01" min="0" value="{{ old('unit_price', $plant->unit_price ?? '') }}" required>
@error('unit_price') <p class="error">{{ $message }}</p> @enderror

<label for="description">Description</label>
<textarea id="description" name="description" rows="4" maxlength="10000" required>{{ old('description', $plant->description ?? '') }}</textarea>
@error('description') <p class="error">{{ $message }}</p> @enderror

<label for="stock_status">Stock status</label>
<select id="stock_status" name="stock_status" required>
    <option value="in_stock" @selected(old('stock_status', $plant->stock_status ?? 'in_stock') === 'in_stock')>In stock</option>
    <option value="out_of_stock" @selected(old('stock_status', $plant->stock_status ?? '') === 'out_of_stock')>Out of stock</option>
</select>
@error('stock_status') <p class="error">{{ $message }}</p> @enderror

<p><button type="submit">{{ $submitLabel }}</button></p>
